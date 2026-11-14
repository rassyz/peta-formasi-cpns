<?php

namespace App\Http\Controllers;

use App\Models\Formasi;
use App\Models\Jurusan;
use App\Models\Musim;
use App\Models\User;
use App\Services\CekKelayakan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class FormasiController extends Controller
{
    /**
     * Detail formasi. Aturan paywall PRD §3/§5: data premium (kualifikasi,
     * syarat, gaji, snapshot, checklist) HANYA disusun & dikirim ke view bila
     * detail terbuka. Di Tahap 3 pembuka satu-satunya adalah kartu contoh
     * (FR-A8); pembukaan via pass menyusul di Tahap 4.
     */
    public function show(string $slug): View
    {
        $musimDefault = Musim::defaultTampil();

        $kandidat = Formasi::query()
            ->where('slug', $slug)
            ->with(['instansi', 'musim', 'kuota'])
            ->get();

        $formasi = $kandidat->firstWhere('musim_id', $musimDefault?->id) ?? $kandidat->first();

        /** @var User|null $pengguna */
        $pengguna = Auth::user();

        abort_if($formasi === null, 404);
        abort_if($formasi->status_terbit !== 'terbit' && ! $pengguna?->isAdmin(), 404);

        $terbuka = (bool) $formasi->kartu_contoh
            || ($pengguna !== null && $pengguna->punyaAksesMusim($formasi->musim));

        // Cek Kelayakan lebih sempit daripada akses detail: khusus Pass
        // Strategi aktif untuk musim formasi ini (PRD Modul B).
        $dapatCek = $pengguna !== null && $pengguna->passes()
            ->where('tipe_pass', 'strategi')
            ->where('status', 'aktif')
            ->whereHas('kodeAkses', fn($q) => $q->where('musim_id', $formasi->musim_id))
            ->get()
            ->contains(fn($pass) => $pass->isAktif());

        $kelayakan = null;
        $detail = null;

        if ($terbuka) {
            $formasi->load(['jurusan', 'kuota.snapshot']);

            // Checklist efektif: baris default instansi digabung baris milik
            // formasi — baris formasi dengan nama dokumen yang sama MENIMPA
            // baris default; nama baru ditambahkan (warisan per baris, PRD §7.2).
            $checklist = $formasi->instansi->checklistDefault()->get()
                ->keyBy('nama_dokumen');
            foreach ($formasi->checklistOverride()->get() as $baris) {
                $checklist[$baris->nama_dokumen] = $baris;
            }
            $checklist = $checklist->values()->sortBy('urutan')->values();

            $detail = [
                'kualifikasi' => $formasi->jurusan,
                'checklist' => $checklist,
                'kuota_snapshot' => $formasi->kuota->map(function ($kuota) {
                    return [
                        'jenis' => $kuota->jenis_kebutuhan,
                        'jumlah' => $kuota->jumlah,
                        'snapshot_terakhir' => $kuota->snapshotTerakhir,
                        'riwayat' => $kuota->snapshot->sortByDesc('tanggal_snapshot')->values(),
                        'rasio' => $kuota->rasio(),
                        'peluang' => $kuota->peluangPersen(),
                    ];
                }),
            ];
        }

        // Hasil per aturan hanya disusun untuk pemegang Strategi yang sudah
        // menjalankan cek di Finder pada sesi yang sama. Profil tidak dibaca
        // dari database dan tidak pernah disimpan sebagai data akun.
        if ($terbuka && $dapatCek) {
            $profil = Session::get('profil_kelayakan');
            $jurusanProfil = is_array($profil)
                ? Jurusan::query()->find($profil['jurusan_id'] ?? null)
                : null;

            if ($profil !== null && $jurusanProfil !== null) {
                $formasi->loadMissing(['jurusan', 'kuota', 'musim']);
                $kelayakan = app(CekKelayakan::class)->nilai($formasi, $jurusanProfil, $profil);
            }
        }

        return view('formasi.detail', [
            'formasi' => $formasi,
            'terbuka' => $terbuka,
            'detail' => $detail,
            'dapatCek' => $dapatCek,
            'kelayakan' => $kelayakan,
        ]);
    }

    /** Halaman SEO per jurusan (versi gratis): daftar polos musim default. */
    public function jurusan(string $slug): View
    {
        $jurusan = Jurusan::query()->where('slug', $slug)->firstOrFail();
        $musim = Musim::defaultTampil();

        $formasi = collect();
        if ($musim) {
            $formasi = Formasi::query()
                ->where('musim_id', $musim->id)
                ->terbit()
                ->whereHas('jurusan', fn($q) => $q->where('jurusan.id', $jurusan->id))
                ->with(['instansi', 'musim', 'kuota'])
                ->orderBy('jabatan')
                ->get();
        }

        return view('jurusan.show', [
            'jurusan' => $jurusan,
            'musim' => $musim,
            'formasi' => $formasi,
        ]);
    }
}
