<?php

namespace App\Livewire\Admin;

use App\Models\Formasi;
use App\Models\Instansi;
use App\Models\Musim;
use App\Models\SnapshotPelamar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Impor snapshot pelamar massal dari CSV: unggah -> analisis (pencocokan
 * formasi + jenis kebutuhan, validasi angka/tanggal, deteksi duplikat
 * melawan snapshot yang sudah tercatat) -> terapkan sekaligus dalam satu
 * transaksi. Snapshot selalu menjadi baris baru bertanggal pada
 * formasi_kuota dan tidak pernah menimpa snapshot lama (FR-C1).
 */
#[Layout('components.layouts.admin')]
#[Title('Impor Snapshot Pelamar — Admin Peta Formasi')]
class ImporSnapshot extends Component
{
    use WithFileUploads;

    public ?int $musimId = null;

    public ?int $instansiId = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $berkas = null; // unggahan CSV

    /** @var array<string, mixed>|null */
    public ?array $laporan = null;

    /** @var array<int, array<string, mixed>> baris yang lolos validasi & siap diterapkan */
    public array $barisSiap = [];

    public function mount(): void
    {
        $this->musimId = Musim::defaultTampil()?->id;
        $this->instansiId = Instansi::query()->orderBy('nama')->first()?->id;
    }

    public function analisis(): void
    {
        $this->laporan = null;
        $this->barisSiap = [];
        $this->validate([
            'musimId' => ['required', 'exists:musim,id'],
            'instansiId' => ['required', 'exists:instansi,id'],
            'berkas' => ['required', 'file', 'max:5120'],
        ]);

        // --- Peta formasi pada musim+instansi terpilih (sekali muat) ---
        $formasiList = Formasi::query()
            ->where('musim_id', $this->musimId)
            ->where('instansi_id', $this->instansiId)
            ->with('kuota')
            ->get();

        $perSlug = [];
        $perNama = [];
        foreach ($formasiList as $formasi) {
            $perSlug[$formasi->slug] = $formasi;
            // Kunci nama dihitung dari jabatan+penempatan (bukan slug tersimpan)
            // supaya formasi berslug sufiks dedup tetap tercocokkan.
            $perNama[Str::slug($formasi->jabatan . ' ' . $formasi->penempatan)][] = $formasi;
        }

        // --- Snapshot yang sudah tercatat untuk kuota-kuota itu (sekali muat) ---
        $kuotaIds = $formasiList->flatMap(fn ($f) => $f->kuota->pluck('id'))->all();
        $snapshotAda = [];
        if ($kuotaIds !== []) {
            $snapshotAda = SnapshotPelamar::query()
                ->whereIn('formasi_kuota_id', $kuotaIds)
                ->get(['formasi_kuota_id', 'tanggal_snapshot', 'tipe'])
                ->mapWithKeys(fn ($s) => [
                    $s->formasi_kuota_id . '|' . $s->tanggal_snapshot->format('Y-m-d') . '|' . $s->tipe => true,
                ])
                ->all();
        }

        $path = $this->berkas->getRealPath();
        $tangani = fopen($path, 'r');
        $barisHeader = fgets($tangani);
        $pembatas = substr_count($barisHeader, ';') > substr_count($barisHeader, ',') ? ';' : ',';
        rewind($tangani);

        $header = null;
        $barisLaporan = [];
        $kunciTerlihat = [];
        $totalMs = 0;
        $totalTotal = 0;
        $barisDenganTotal = 0;
        $semuaValid = true;
        $nomor = 0;

        while (($kolom = fgetcsv($tangani, 0, $pembatas)) !== false) {
            $nomor++;
            if ($header === null) {
                $header = array_map(fn ($h) => strtolower(trim($h)), $kolom);
                continue;
            }
            if (count(array_filter($kolom, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue; // baris kosong
            }

            $data = [];
            foreach ($header as $i => $nama) {
                $data[$nama] = isset($kolom[$i]) ? trim((string) $kolom[$i]) : '';
            }

            $galat = [];
            $jabatan = $data['jabatan'] ?? '';
            $penempatan = $data['penempatan'] ?? '';
            $slugIsi = $data['slug'] ?? '';
            $jenis = strtolower($data['jenis_kebutuhan'] ?? '');
            $tipe = strtolower($data['tipe'] ?? '');
            $tanggal = $data['tanggal_snapshot'] ?? '';
            $sumber = ($data['sumber'] ?? '') !== '' ? $data['sumber'] : null;

            // --- Pencocokan formasi ---
            $formasi = null;
            if ($slugIsi !== '') {
                $formasi = $perSlug[$slugIsi] ?? null;
                if ($formasi === null) {
                    $galat[] = "Slug \"{$slugIsi}\" tidak ditemukan pada musim+instansi ini.";
                }
            } elseif ($jabatan === '' || $penempatan === '') {
                $galat[] = 'Jabatan dan penempatan wajib diisi (atau isi kolom slug).';
            } else {
                $kandidat = $perNama[Str::slug($jabatan . ' ' . $penempatan)] ?? [];
                if ($kandidat === []) {
                    $galat[] = 'Formasi tidak ditemukan untuk jabatan & penempatan ini.';
                } elseif (count($kandidat) > 1) {
                    $galat[] = 'Jabatan & penempatan ini ambigu (' . count($kandidat) . ' formasi) — isi kolom slug untuk menegaskan.';
                } else {
                    $formasi = $kandidat[0];
                }
            }

            // --- Pencocokan kuota per jenis kebutuhan ---
            $kuota = null;
            if ($jenis === '') {
                $galat[] = 'Jenis kebutuhan wajib diisi.';
            } elseif ($formasi !== null) {
                $kuota = $formasi->kuota->firstWhere('jenis_kebutuhan', $jenis);
                if ($kuota === null) {
                    $galat[] = "Formasi ini tidak punya kuota jenis \"{$jenis}\".";
                }
            }

            // --- Tanggal & tipe ---
            $tanggalValid = false;
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                [$th, $bl, $tg] = array_map('intval', explode('-', $tanggal));
                $tanggalValid = checkdate($bl, $tg, $th);
            }
            if (! $tanggalValid) {
                $galat[] = 'Tanggal snapshot tidak valid (format YYYY-MM-DD).';
            }
            if (! in_array($tipe, ['historis', 'live'], true)) {
                $galat[] = 'Tipe harus historis atau live.';
            }

            // --- Angka ---
            $jumlahMs = null;
            if (($data['jumlah_ms'] ?? '') === '') {
                $galat[] = 'Jumlah MS wajib diisi.';
            } elseif (! preg_match('/^\d+$/', $data['jumlah_ms'])) {
                $galat[] = 'Jumlah MS bukan angka bulat valid.';
            } else {
                $jumlahMs = (int) $data['jumlah_ms'];
            }

            $jumlahTotal = null;
            if (($data['jumlah_total'] ?? '') !== '') {
                if (! preg_match('/^\d+$/', $data['jumlah_total'])) {
                    $galat[] = 'Jumlah total bukan angka bulat valid.';
                } else {
                    $jumlahTotal = (int) $data['jumlah_total'];
                    if ($jumlahMs !== null && $jumlahTotal < $jumlahMs) {
                        $galat[] = 'Jumlah total lebih kecil dari jumlah MS.';
                    }
                }
            }

            if ($sumber !== null && Str::length($sumber) > 160) {
                $galat[] = 'Sumber terlalu panjang (maks 160 karakter).';
            }

            // --- Duplikat di dalam berkas & melawan database ---
            if ($kuota !== null && $tanggalValid && in_array($tipe, ['historis', 'live'], true)) {
                $kunci = $kuota->id . '|' . $tanggal . '|' . $tipe;
                if (isset($kunciTerlihat[$kunci])) {
                    $galat[] = 'Duplikat di dalam berkas (baris ' . $kunciTerlihat[$kunci] . ').';
                } else {
                    $kunciTerlihat[$kunci] = $nomor;
                }
                if (isset($snapshotAda[$kunci])) {
                    $galat[] = "Snapshot tanggal {$tanggal} (tipe {$tipe}) untuk kuota ini sudah ada di database.";
                }
            }

            if ($galat !== []) {
                $semuaValid = false;
            } else {
                $this->barisSiap[] = [
                    'formasi_kuota_id' => $kuota->id,
                    'tipe' => $tipe,
                    'jumlah_ms' => $jumlahMs,
                    'jumlah_total' => $jumlahTotal,
                    'tanggal_snapshot' => $tanggal,
                    'sumber' => $sumber,
                ];
                $totalMs += $jumlahMs;
                if ($jumlahTotal !== null) {
                    $totalTotal += $jumlahTotal;
                    $barisDenganTotal++;
                }
            }

            $barisLaporan[] = [
                'nomor' => $nomor,
                'jabatan' => $jabatan,
                'penempatan' => $penempatan,
                'jenis' => $jenis,
                'tanggal' => $tanggal,
                'galat' => $galat,
            ];
        }
        fclose($tangani);

        $this->laporan = [
            'baris' => $barisLaporan,
            'jumlah_baris' => count($barisLaporan),
            'jumlah_siap' => count($this->barisSiap),
            'jumlah_bermasalah' => count($barisLaporan) - count($this->barisSiap),
            'total_ms' => $totalMs,
            'total_total' => $totalTotal,
            'baris_dengan_total' => $barisDenganTotal,
            'valid' => $semuaValid && count($barisLaporan) > 0,
        ];
    }

    public function terapkan(): void
    {
        if ($this->laporan === null || ! $this->laporan['valid'] || $this->barisSiap === []) {
            $this->dispatch('toast', tipe: 'peringatan', pesan: 'Belum ada baris valid yang bisa diterapkan — perbaiki berkas dulu.');
            return;
        }

        DB::transaction(function () {
            foreach ($this->barisSiap as $baris) {
                SnapshotPelamar::create($baris);
            }
        });

        $jumlah = count($this->barisSiap);
        $this->dispatch('toast', tipe: 'sukses', pesan: $jumlah . ' snapshot pelamar diterapkan sebagai baris baru bertanggal.');
        $this->laporan = null;
        $this->barisSiap = [];
        $this->berkas = null;
    }

    public function render()
    {
        return view('livewire.admin.impor-snapshot', [
            'musimList' => Musim::query()->orderByDesc('tahun')->get(),
            'instansiList' => Instansi::query()->orderBy('nama')->get(),
        ]);
    }
}
