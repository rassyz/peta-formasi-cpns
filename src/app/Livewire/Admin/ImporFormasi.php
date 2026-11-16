<?php

namespace App\Livewire\Admin;

use App\Models\Formasi;
use App\Models\Instansi;
use App\Models\Jurusan;
use App\Models\Musim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Impor formasi dari spreadsheet CSV (PRD §10): unggah -> analisis & pratinjau
 * (validasi baris, deteksi duplikat, pencocokan jurusan, gerbang checksum
 * kuota) -> terapkan sebagai DRAFT. Ekstraksi PDF dilakukan di luar sistem;
 * halaman ini hanya menerima hasil akhirnya.
 */
#[Layout('components.layouts.admin')]
#[Title('Impor Formasi — Admin Peta Formasi')]
class ImporFormasi extends Component
{
    use WithFileUploads;

    public ?int $musimId = null;

    public ?int $instansiId = null;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $berkas = null; // unggahan CSV

    public ?int $targetKuota = null; // total resmi dari pengumuman (gerbang checksum)

    /** @var array<string, mixed>|null */
    public ?array $laporan = null;

    /** @var array<int, array<string, mixed>> baris yang lolos validasi & siap diterapkan */
    public array $barisSiap = [];


    public function mount(): void
    {
        $this->musimId = Musim::defaultTampil()?->id;
        $this->instansiId = Instansi::query()->orderBy('nama')->first()?->id;
    }

    private function labelJurusanKeSlug(string $label): string
    {
        return Str::slug(trim($label));
    }

    public function analisis(): void
    {
        $this->laporan = null;
        $this->barisSiap = [];
        $this->validate([
            'musimId' => ['required', 'exists:musim,id'],
            'instansiId' => ['required', 'exists:instansi,id'],
            'berkas' => ['required', 'file', 'max:5120'],
            'targetKuota' => ['nullable', 'integer', 'min:0'],
        ]);

        $path = $this->berkas->getRealPath();
        $tangani = fopen($path, 'r');
        $barisHeader = fgets($tangani);
        $pembatas = substr_count($barisHeader, ';') > substr_count($barisHeader, ',') ? ';' : ',';
        rewind($tangani);

        $header = null;
        $barisLaporan = [];
        $jurusanHilang = [];
        $totalKuotaFile = 0;
        $kunciTerlihat = [];
        $semuaValid = true;
        $nomor = 0;

        while (($kolom = fgetcsv($tangani, 0, $pembatas)) !== false) {
            $nomor++;
            if ($header === null) {
                $header = array_map(fn($h) => strtolower(trim($h)), $kolom);
                continue;
            }
            if (count(array_filter($kolom, fn($v) => trim((string) $v) !== '')) === 0) {
                continue; // baris kosong
            }

            $data = [];
            foreach ($header as $i => $nama) {
                $data[$nama] = isset($kolom[$i]) ? trim((string) $kolom[$i]) : '';
            }

            $galat = [];
            $jabatan = $data['jabatan'] ?? '';
            $penempatan = $data['penempatan'] ?? '';
            if ($jabatan === '' || $penempatan === '') {
                $galat[] = 'Jabatan dan penempatan wajib diisi.';
            }

            // --- Kuota per jenis ---
            $kuota = [];
            foreach (['umum', 'cumlaude', 'disabilitas'] as $jenis) {
                $nilai = $data['kuota_' . $jenis] ?? '';
                if ($nilai !== '') {
                    if (! is_numeric($nilai) || (int) $nilai < 0) {
                        $galat[] = "Kuota {$jenis} bukan angka valid.";
                    } elseif ((int) $nilai > 0) {
                        $kuota[$jenis] = (int) $nilai;
                    }
                }
            }
            if (($data['kuota_lainnya'] ?? '') !== '') {
                foreach (explode(';', $data['kuota_lainnya']) as $pasangan) {
                    $pasangan = trim($pasangan);
                    if ($pasangan === '') {
                        continue;
                    }
                    if (! str_contains($pasangan, ':')) {
                        $galat[] = "Format kuota_lainnya salah pada \"{$pasangan}\" (harus jenis:jumlah).";
                        continue;
                    }
                    [$jenisLain, $nilaiLain] = array_map('trim', explode(':', $pasangan, 2));
                    if (! is_numeric($nilaiLain) || (int) $nilaiLain < 0) {
                        $galat[] = "Kuota {$jenisLain} bukan angka valid.";
                    } elseif ((int) $nilaiLain > 0) {
                        $kuota[Str::slug($jenisLain)] = (int) $nilaiLain;
                    }
                }
            }
            $totalBaris = array_sum($kuota);
            if ($totalBaris <= 0 && $galat === []) {
                $galat[] = 'Total kuota baris ini 0 — formasi tanpa kuota tidak diimpor.';
            }

            // --- Kualifikasi -> jurusan master ---
            $jurusanIds = [];
            $labelJurusan = array_filter(array_map('trim', explode('|', $data['kualifikasi'] ?? '')));
            if ($labelJurusan === []) {
                $galat[] = 'Kualifikasi kosong.';
            }
            foreach ($labelJurusan as $label) {
                $jurusan = Jurusan::query()->where('slug', $this->labelJurusanKeSlug($label))->first();
                if ($jurusan === null) {
                    $jurusanHilang[$label] = true;
                    $galat[] = "Jurusan \"{$label}\" belum ada di master.";
                } else {
                    $jurusanIds[] = $jurusan->id;
                }
            }

            // --- Duplikat di dalam berkas & melawan database ---
            $slug = Str::slug($jabatan . ' ' . $penempatan);
            $kunci = $slug . '|' . implode(',', $jurusanIds);
            if (isset($kunciTerlihat[$kunci])) {
                $galat[] = 'Duplikat di dalam berkas (baris ' . $kunciTerlihat[$kunci] . ').';
            } else {
                $kunciTerlihat[$kunci] = $nomor;
            }
            $sudahAda = Formasi::query()
                ->where('musim_id', $this->musimId)
                ->where('instansi_id', $this->instansiId)
                ->where('slug', $slug)
                ->exists();
            if ($sudahAda) {
                $galat[] = 'Sudah ada di database untuk musim+instansi ini (jabatan & penempatan sama).';
            }

            // --- Angka & tanggal opsional ---
            $ipk = null;
            if (($data['ipk_min'] ?? '') !== '') {
                $ipkKandidat = str_replace(',', '.', $data['ipk_min']);
                if (! is_numeric($ipkKandidat)) {
                    $galat[] = 'IPK minimum bukan angka valid.';
                } else {
                    $ipk = (float) $ipkKandidat;
                }
            }

            $totalKuotaFile += $totalBaris;
            if ($galat !== []) {
                $semuaValid = false;
            } else {
                $this->barisSiap[] = [
                    'jabatan' => $jabatan,
                    'penempatan' => $penempatan,
                    'slug' => $slug,
                    'jurusan_ids' => $jurusanIds,
                    'kuota' => $kuota,
                    'usia_min' => ($data['usia_min'] ?? '') !== '' ? (int) $data['usia_min'] : null,
                    'usia_maks' => ($data['usia_maks'] ?? '') !== '' ? (int) $data['usia_maks'] : null,
                    'ipk_min' => $ipk,
                    'skl_diterima' => strtolower($data['skl_diterima'] ?? '') === 'ya',
                    'syarat_akreditasi' => ($data['syarat_akreditasi'] ?? '') !== '' ? $data['syarat_akreditasi'] : null,
                    'syarat_khusus' => ($data['syarat_khusus'] ?? '') !== '' ? $data['syarat_khusus'] : null,
                    'deskripsi_tugas' => ($data['deskripsi_tugas'] ?? '') !== '' ? $data['deskripsi_tugas'] : null,
                    'gaji_min' => ($data['gaji_min'] ?? '') !== '' ? (int) $data['gaji_min'] : null,
                    'gaji_maks' => ($data['gaji_maks'] ?? '') !== '' ? (int) $data['gaji_maks'] : null,
                    'sumber_gaji' => ($data['sumber_gaji'] ?? '') !== '' ? $data['sumber_gaji'] : null,
                    'tanggal_verifikasi_gaji' => ($data['tanggal_verifikasi_gaji'] ?? '') !== '' ? $data['tanggal_verifikasi_gaji'] : null,
                    'sumber_pengumuman' => ($data['sumber_pengumuman'] ?? '') !== '' ? $data['sumber_pengumuman'] : null,
                    'tanggal_verifikasi' => ($data['tanggal_verifikasi'] ?? '') !== '' ? $data['tanggal_verifikasi'] : null,
                    'kartu_contoh' => strtolower($data['kartu_contoh'] ?? '') === 'ya',
                ];
            }

            $barisLaporan[] = [
                'nomor' => $nomor,
                'jabatan' => $jabatan,
                'penempatan' => $penempatan,
                'total_kuota' => $totalBaris,
                'galat' => $galat,
            ];
        }
        fclose($tangani);

        $checksumOk = $this->targetKuota === null ? null : ($totalKuotaFile === $this->targetKuota);

        $this->laporan = [
            'baris' => $barisLaporan,
            'jumlah_baris' => count($barisLaporan),
            'jumlah_siap' => count($this->barisSiap),
            'jumlah_bermasalah' => count($barisLaporan) - count($this->barisSiap),
            'total_kuota_file' => $totalKuotaFile,
            'target_kuota' => $this->targetKuota,
            'checksum_ok' => $checksumOk,
            'jurusan_hilang' => array_keys($jurusanHilang),
            'valid' => $semuaValid && count($barisLaporan) > 0,
        ];
    }

    /** Buat jurusan master yang hilang dari label berkas, lalu analisis ulang. */
    public function tambahJurusanHilang(): void
    {
        foreach (($this->laporan['jurusan_hilang'] ?? []) as $label) {
            $bagian = preg_split('/\s+/', trim($label), 2);
            $jenjang = $bagian[0];
            $nama = $bagian[1] ?? $label;
            Jurusan::query()->firstOrCreate(
                ['slug' => $this->labelJurusanKeSlug($label)],
                ['jenjang' => $jenjang, 'nama' => $nama]
            );
        }

        $this->dispatch('toast', tipe: 'sukses', pesan: 'Jurusan yang hilang sudah ditambahkan ke master. Analisis dijalankan ulang.');
        $this->analisis();
    }

    public function terapkan(): void
    {
        if ($this->laporan === null || ! $this->laporan['valid'] || $this->barisSiap === []) {
            $this->dispatch('toast', tipe: 'peringatan', pesan: 'Belum ada baris valid yang bisa diterapkan — perbaiki berkas dulu.');
            return;
        }
        // Gerbang checksum: target terisi tapi tidak cocok -> penerapan DIBLOKIR.
        if ($this->laporan['checksum_ok'] === false) {
            $this->dispatch('toast', tipe: 'galat', pesan: 'Checksum tidak cocok: total kuota berkas '
                . number_format($this->laporan['total_kuota_file'], 0, ',', '.')
                . ' vs target resmi ' . number_format($this->laporan['target_kuota'], 0, ',', '.')
                . '. Penerapan diblokir — periksa kembali ekstraksi datanya (PRD §10).');
            return;
        }

        DB::transaction(function () {
            foreach ($this->barisSiap as $baris) {
                $slug = $baris['slug'];
                $pagar = 2;
                while (Formasi::query()->where('musim_id', $this->musimId)->where('slug', $slug)->exists()) {
                    $slug = $baris['slug'] . '-' . $pagar++;
                }

                $formasi = Formasi::create([
                    'musim_id' => $this->musimId,
                    'instansi_id' => $this->instansiId,
                    'jabatan' => $baris['jabatan'],
                    'penempatan' => $baris['penempatan'],
                    'slug' => $slug,
                    'usia_min' => $baris['usia_min'],
                    'usia_maks' => $baris['usia_maks'],
                    'ipk_min' => $baris['ipk_min'],
                    'skl_diterima' => $baris['skl_diterima'],
                    'syarat_akreditasi' => $baris['syarat_akreditasi'],
                    'syarat_khusus' => $baris['syarat_khusus'],
                    'deskripsi_tugas' => $baris['deskripsi_tugas'],
                    'gaji_min' => $baris['gaji_min'],
                    'gaji_maks' => $baris['gaji_maks'],
                    'sumber_gaji' => $baris['sumber_gaji'],
                    'tanggal_verifikasi_gaji' => $baris['tanggal_verifikasi_gaji'],
                    'sumber_pengumuman' => $baris['sumber_pengumuman'],
                    'tanggal_verifikasi' => $baris['tanggal_verifikasi'],
                    'status_terbit' => 'draft',
                    'kartu_contoh' => $baris['kartu_contoh'],
                ]);

                $formasi->jurusan()->sync($baris['jurusan_ids']);

                foreach ($baris['kuota'] as $jenis => $jumlah) {
                    $formasi->kuota()->create([
                        'jenis_kebutuhan' => $jenis,
                        'jumlah' => $jumlah,
                    ]);
                }
            }
        });

        $jumlah = count($this->barisSiap);
        $this->dispatch('toast', tipe: 'sukses', pesan: $jumlah . ' formasi diterapkan sebagai DRAFT. Terbitkan dari halaman Kelola Formasi setelah diperiksa.');
        $this->laporan = null;
        $this->barisSiap = [];
        $this->berkas = null;
    }

    public function render()
    {
        return view('livewire.admin.impor-formasi', [
            'musimList' => Musim::query()->orderByDesc('tahun')->get(),
            'instansiList' => Instansi::query()->orderBy('nama')->get(),
        ]);
    }
}
