<?php

namespace App\Livewire;

use App\Models\Formasi;
use App\Models\Instansi;
use App\Models\Jurusan;
use App\Models\Musim;
use App\Models\User;
use App\Services\CekKelayakan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Finder formasi (Modul A, FR-A1 s.d. A5). Seluruh fungsi daftar GRATIS:
 * pilih jurusan, pemilih musim, filter instansi/wilayah/jenis kebutuhan/
 * 0 pelamar, dan sortir peluang/gaji/kuota. Kartu selalu polos — angka
 * pelamar hanya dipakai di server untuk filter & urutan (PRD §5), tidak
 * pernah dirender ke kartu.
 */
#[Layout('components.layouts.public')]
#[Title('Cari Formasi — Peta Formasi')]
class Finder extends Component
{
    use WithPagination;

    #[Url(as: 'jurusan')]
    public string $jurusanSlug = '';

    #[Url(as: 'musim')]
    public ?int $musimId = null;

    // String (bukan ?int): pilihan "Semua instansi" bernilai '' dan Livewire
    // akan memaksa '' menjadi 0 pada properti int — 0 tidak boleh berarti filter.
    #[Url]
    public string $instansiId = '';

    #[Url]
    public string $wilayah = ''; // '' | pusat | daerah

    #[Url]
    public string $jenis = ''; // '' | umum | cumlaude | disabilitas | ...

    #[Url(as: 'nol')]
    public bool $hanyaNolPelamar = false;

    #[Url]
    public string $urut = 'peluang'; // peluang | gaji | kuota

    // Pencarian nama formasi (amendemen FR-A3, 15 Nov 2026): teks bebas pada
    // jabatan/penempatan/instansi. Boleh berdiri sendiri tanpa jurusan
    // (mode nama, aktif mulai 2 huruf) atau menjadi saringan tambahan di
    // dalam hasil jurusan terpilih.
    #[Url(as: 'nama')]
    public string $kataKunci = '';

    // --- Profil Cek Kelayakan (Modul B, FR-B1 s.d. B4) ---
    // Sengaja TANPA #[Url] dan TANPA tabel database: profil hanya hidup di
    // session browser ini, sesuai keputusan PRD Bagian 6 & 11.
    public string $profilTanggalLahir = '';

    public string $profilIpk = '';

    public string $profilDokumen = 'ijazah'; // ijazah | skl

    public string $profilAkreditasi = 'terakreditasi'; // terakreditasi | tidak | tidak_tahu

    public string $profilJenis = 'umum';

    public bool $profilAktif = false;

    public bool $hanyaLolos = false;

    public string $pesanProfil = '';

    public function mount(): void
    {
        if (! $this->musimId) {
            $this->musimId = Musim::defaultTampil()?->id;
        }

        $profil = Session::get('profil_kelayakan');
        if (is_array($profil)) {
            $this->profilTanggalLahir = (string) ($profil['tanggal_lahir'] ?? '');
            $this->profilIpk = isset($profil['ipk'])
                ? rtrim(rtrim(number_format((float) $profil['ipk'], 2, '.', ''), '0'), '.')
                : '';
            $this->profilDokumen = (string) ($profil['dokumen'] ?? 'ijazah');
            $this->profilAkreditasi = (string) ($profil['akreditasi'] ?? 'terakreditasi');
            $this->profilJenis = (string) ($profil['jenis_kebutuhan'] ?? 'umum');
            $this->profilAktif = true;
        }
    }

    /** Profil sesi hanya dikembalikan setelah pengguna benar-benar menjalankan cek. */
    private function profilSesi(): ?array
    {
        if (! $this->profilAktif) {
            return null;
        }

        $profil = Session::get('profil_kelayakan');

        return is_array($profil) ? $profil : null;
    }

    /** Cek kelayakan khusus pemegang Pass STRATEGI aktif pada musim terpilih. */
    private function bolehCek(?Musim $musim): bool
    {
        /** @var User|null $pengguna */
        $pengguna = Auth::user();

        if ($pengguna === null || $musim === null) {
            return false;
        }

        return $pengguna->passes()
            ->where('tipe_pass', 'strategi')
            ->where('status', 'aktif')
            ->whereHas('kodeAkses', fn($q) => $q->where('musim_id', $musim->id))
            ->get()
            ->contains(fn($pass) => $pass->isAktif());
    }

    public function jalankanCek(): void
    {
        $musim = Musim::query()->find($this->musimId);
        $jurusan = Jurusan::query()->where('slug', $this->jurusanSlug)->first();

        if (! $this->bolehCek($musim)) {
            $this->pesanProfil = 'Cek kelayakan memerlukan Pass Strategi aktif untuk musim yang sedang dipilih.';
            $this->profilAktif = false;
            return;
        }

        if ($jurusan === null) {
            $this->pesanProfil = 'Pilih jurusan terlebih dahulu sebelum menjalankan cek.';
            return;
        }

        // Terima IPK dengan koma Indonesia ("3,20") maupun titik ("3.20").
        $this->profilIpk = str_replace(',', '.', trim($this->profilIpk));

        $this->validate([
            'profilTanggalLahir' => ['required', 'date', 'before:today'],
            'profilIpk' => ['required', 'numeric', 'min:0', 'max:4'],
            'profilDokumen' => ['required', 'in:ijazah,skl'],
            'profilAkreditasi' => ['required', 'in:terakreditasi,tidak,tidak_tahu'],
            'profilJenis' => ['required', 'string', 'max:40'],
        ], [], [
            'profilTanggalLahir' => 'tanggal lahir',
            'profilIpk' => 'IPK',
            'profilDokumen' => 'status dokumen kelulusan',
            'profilAkreditasi' => 'status akreditasi',
            'profilJenis' => 'jenis kebutuhan',
        ]);

        Session::put('profil_kelayakan', [
            'tanggal_lahir' => $this->profilTanggalLahir,
            'ipk' => (float) $this->profilIpk,
            'dokumen' => $this->profilDokumen,
            'akreditasi' => $this->profilAkreditasi,
            'jenis_kebutuhan' => $this->profilJenis,
            // Konteks jurusan saat cek dijalankan; dipakai halaman detail.
            // Di Finder sendiri, penilaian selalu memakai jurusan terpilih saat ini.
            'jurusan_id' => $jurusan->id,
        ]);

        $this->profilAktif = true;
        $this->hanyaLolos = false;
        $this->pesanProfil = 'Cek kelayakan aktif. Badge pada kartu dan hasil per aturan di detail sekarang memakai profil sesi ini.';
        $this->resetPage();
    }

    public function hapusProfil(): void
    {
        Session::forget('profil_kelayakan');
        $this->profilTanggalLahir = '';
        $this->profilIpk = '';
        $this->profilDokumen = 'ijazah';
        $this->profilAkreditasi = 'terakreditasi';
        $this->profilJenis = 'umum';
        $this->profilAktif = false;
        $this->hanyaLolos = false;
        $this->pesanProfil = 'Profil sesi dihapus. Kartu kembali tampil tanpa penilaian.';
        $this->resetPage();
    }

    public function updated(string $properti): void
    {
        if ($properti !== 'page') {
            $this->resetPage();
        }

        // Ganti musim: saring ulang pilihan yang mungkin tidak ada di musim baru.
        if ($properti === 'musimId') {
            $this->instansiId = '';
            $this->jenis = '';
        }

        if ($properti === 'hanyaLolos' && ! $this->profilAktif) {
            $this->hanyaLolos = false;
        }
    }

    /** Kondisi "snapshot terakhir pada suatu kuota berisi 0 pelamar MS". */
    private function kondisiNolPelamar(Builder $query): void
    {
        $query->whereHas('kuota', function (Builder $q) {
            $q->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('snapshot_pelamar')
                    ->whereColumn('snapshot_pelamar.formasi_kuota_id', 'formasi_kuota.id')
                    ->where('snapshot_pelamar.jumlah_ms', 0)
                    ->whereRaw('snapshot_pelamar.tanggal_snapshot = (SELECT MAX(tanggal_snapshot) FROM snapshot_pelamar sp2 WHERE sp2.formasi_kuota_id = formasi_kuota.id)');
            });
        });
    }

    private function queryDasar(?Jurusan $jurusan): Builder
    {
        $query = Formasi::query()
            ->where('musim_id', $this->musimId)
            ->terbit();

        if ($jurusan !== null) {
            $query->whereHas('jurusan', fn(Builder $q) => $q->where('jurusan.id', $jurusan->id));
        }

        $kata = trim($this->kataKunci);
        if ($kata !== '') {
            $query->where(function (Builder $q) use ($kata) {
                $q->where('formasi.jabatan', 'like', '%' . $kata . '%')
                    ->orWhere('formasi.penempatan', 'like', '%' . $kata . '%')
                    ->orWhereHas('instansi', fn(Builder $i) => $i->where('nama', 'like', '%' . $kata . '%'));
            });
        }

        if ($this->instansiId !== '') {
            $query->where('instansi_id', (int) $this->instansiId);
        }

        if ($this->wilayah !== '') {
            $query->whereHas('instansi', fn(Builder $q) => $q->where('wilayah', $this->wilayah));
        }

        if ($this->jenis !== '') {
            $query->whereHas('kuota', fn(Builder $q) => $q
                ->where('jenis_kebutuhan', $this->jenis)
                ->where('jumlah', '>', 0));
        }

        if ($this->hanyaNolPelamar) {
            $this->kondisiNolPelamar($query);
        }

        return $query;
    }

    private function terapkanUrutan(Builder $query): void
    {
        if ($this->urut === 'kuota') {
            $query->withSum('kuota as total_kuota_sum', 'jumlah')
                ->orderByDesc('total_kuota_sum');
            return;
        }

        if ($this->urut === 'gaji') {
            // NULL selalu terakhir (formasi tanpa sumber gaji tidak memimpin).
            $query->orderByRaw('formasi.gaji_maks IS NULL')
                ->orderByDesc('formasi.gaji_maks');
            return;
        }

        // Peluang (FR-C2): kuota / pelamar MS snapshot terakhir pada jenis yang
        // dipilih (default: umum). Tanpa snapshot -> tidak ikut ranking (FR-C4),
        // ditaruh paling belakang; MS 0 -> peluang terbesar, paling depan.
        $jenisPeluang = $this->jenis !== '' ? $this->jenis : 'umum';

        $query->leftJoin('formasi_kuota as fk_p', function ($join) use ($jenisPeluang) {
            $join->on('fk_p.formasi_id', '=', 'formasi.id')
                ->where('fk_p.jenis_kebutuhan', '=', $jenisPeluang);
        })
            ->leftJoin('snapshot_pelamar as sp_p', function ($join) {
                $join->on('sp_p.formasi_kuota_id', '=', 'fk_p.id')
                    ->whereRaw('sp_p.tanggal_snapshot = (SELECT MAX(tanggal_snapshot) FROM snapshot_pelamar WHERE formasi_kuota_id = fk_p.id)');
            })
            ->select('formasi.*')
            ->orderByRaw('CASE WHEN sp_p.jumlah_ms IS NULL THEN 2 WHEN sp_p.jumlah_ms = 0 THEN 0 ELSE 1 END')
            ->orderByRaw('(fk_p.jumlah / NULLIF(sp_p.jumlah_ms, 0)) DESC');
    }

    public function render()
    {
        $musimList = Musim::query()
            ->whereIn('status', ['aktif', 'historis'])
            ->orderByDesc('tahun')
            ->get();

        $jurusanList = Jurusan::query()->orderBy('jenjang')->orderBy('nama')->get();
        $jurusan = $jurusanList->firstWhere('slug', $this->jurusanSlug);

        $instansiList = Instansi::query()
            ->whereHas('formasi', fn(Builder $q) => $q->where('musim_id', $this->musimId)->terbit())
            ->orderBy('nama')
            ->get();

        $jenisList = DB::table('formasi_kuota')
            ->join('formasi', 'formasi.id', '=', 'formasi_kuota.formasi_id')
            ->where('formasi.musim_id', $this->musimId)
            ->where('formasi.status_terbit', 'terbit')
            ->distinct()
            ->orderBy('formasi_kuota.jenis_kebutuhan')
            ->pluck('formasi_kuota.jenis_kebutuhan');

        $hasil = null;
        $jumlahNol = 0;
        $musimAktif = $musimList->firstWhere('id', $this->musimId);
        $bolehCek = $this->bolehCek($musimAktif);
        $profil = $bolehCek ? $this->profilSesi() : null;
        $kelayakanPeta = [];

        // Mode nama: tanpa jurusan, hasil tampil bila kata kunci ≥ 2 huruf.
        $modeNama = $jurusan === null && mb_strlen(trim($this->kataKunci)) >= 2;

        if ($jurusan !== null || $modeNama) {
            $queryNol = $this->queryDasar($jurusan);
            if (! $this->hanyaNolPelamar) {
                $this->kondisiNolPelamar($queryNol);
            }
            $jumlahNol = $queryNol->count();

            $query = $this->queryDasar($jurusan);
            $this->terapkanUrutan($query);

            if ($profil !== null && $this->hanyaLolos && $jurusan !== null) {
                // FR-B3 harus menyaring seluruh hasil, bukan hanya 12 kartu
                // pada halaman aktif. Karena itu nilai dulu semua baris yang
                // cocok dengan filter daftar, baru buat paginator manual.
                $cek = app(CekKelayakan::class);
                $semua = $query
                    ->with(['instansi', 'musim', 'kuota', 'jurusan'])
                    ->get()
                    ->filter(function (Formasi $formasi) use ($cek, $jurusan, $profil, &$kelayakanPeta) {
                        $hasilCek = $cek->nilai($formasi, $jurusan, $profil);
                        $kelayakanPeta[$formasi->id] = $hasilCek;

                        return $hasilCek['status'] === 'lolos';
                    })
                    ->values();

                $halaman = $this->getPage();
                $hasil = new LengthAwarePaginator(
                    $semua->forPage($halaman, 12),
                    $semua->count(),
                    12,
                    $halaman,
                    ['path' => request()->url(), 'pageName' => 'page']
                );
            } else {
                $hasil = $query
                    ->with(['instansi', 'musim', 'kuota', 'jurusan'])
                    ->paginate(12);

                if ($profil !== null && $jurusan !== null) {
                    $cek = app(CekKelayakan::class);
                    foreach ($hasil->items() as $formasi) {
                        $kelayakanPeta[$formasi->id] = $cek->nilai($formasi, $jurusan, $profil);
                    }
                }
            }
        }

        return view('livewire.finder', [
            'musimList' => $musimList,
            'musimAktif' => $musimAktif,
            'bolehCek' => $bolehCek,
            'kelayakanPeta' => $kelayakanPeta,
            'jenisProfilList' => $jenisList->isEmpty() ? collect(['umum']) : $jenisList,
            'jurusanList' => $jurusanList,
            'jurusan' => $jurusan,
            'jurusanItems' => $jurusanList->map(fn(Jurusan $j) => [
                'value' => $j->slug,
                'label' => $j->nama,
                'jenjang' => $j->jenjang,
            ])->all(),
            'instansiList' => $instansiList,
            'jenisList' => $jenisList,
            'hasil' => $hasil,
            'jumlahNol' => $jumlahNol,
            'modeNama' => $modeNama ?? false,
        ]);
    }
}
