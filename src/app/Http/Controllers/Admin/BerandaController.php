<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formasi;
use App\Models\FormasiKuota;
use App\Models\KodeAkses;
use App\Models\Musim;
use App\Models\RequestFormasi;
use App\Models\User;
use Illuminate\View\View;

/**
 * Beranda admin (DESIGN.md §11): ringkasan keadaan data musim yang sedang
 * tampil + pintasan modul. Semua angka dibaca langsung dari database —
 * halaman ini murni pemantau, tidak mengubah apa pun.
 */
class BerandaController extends Controller
{
    public function __invoke(): View
    {
        $musim = Musim::defaultTampil();

        $statistik = [
            'formasi_terbit' => $musim
                ? Formasi::query()->where('musim_id', $musim->id)->terbit()->count()
                : 0,
            'formasi_draft' => $musim
                ? Formasi::query()->where('musim_id', $musim->id)->draft()->count()
                : 0,
            'total_kuota' => $musim
                ? (int) FormasiKuota::query()
                    ->whereHas('formasi', fn ($q) => $q->where('musim_id', $musim->id))
                    ->sum('jumlah')
                : 0,
            'kode_belum_dipakai' => KodeAkses::query()->where('status', 'belum_dipakai')->count(),
            'kode_terpakai' => KodeAkses::query()->where('status', 'terpakai')->count(),
            'request_masuk' => RequestFormasi::query()->count(),
            'peserta' => User::query()->where('role', 'peserta')->count(),
        ];

        $daftarMusim = Musim::query()
            ->withCount([
                'formasi',
                'formasi as formasi_terbit_count' => fn ($q) => $q->terbit(),
            ])
            ->orderByDesc('tahun')
            ->get();

        $requestTerbaru = RequestFormasi::query()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.beranda', [
            'musim' => $musim,
            'statistik' => $statistik,
            'daftarMusim' => $daftarMusim,
            'requestTerbaru' => $requestTerbaru,
        ]);
    }
}
