<?php

namespace App\Http\Controllers;

use App\Models\Formasi;
use App\Models\Jurusan;
use App\Models\Musim;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BerandaController extends Controller
{
    public function __invoke(): View
    {
        $musim = Musim::defaultTampil();

        $statistik = [
            'total_formasi' => 0,
            'formasi_nol_pelamar' => 0,
            'total_kuota' => 0,
            'total_instansi' => 0,
        ];
        $contoh = collect();

        if ($musim) {
            $dasar = Formasi::query()->where('musim_id', $musim->id)->terbit();

            $statistik['total_formasi'] = (clone $dasar)->count();
            $statistik['total_kuota'] = (int) (clone $dasar)
                ->join('formasi_kuota', 'formasi_kuota.formasi_id', '=', 'formasi.id')
                ->sum('formasi_kuota.jumlah');
            $statistik['total_instansi'] = (clone $dasar)->distinct('instansi_id')->count('instansi_id');
            $statistik['formasi_nol_pelamar'] = (clone $dasar)
                ->whereHas('kuota', function ($q) {
                    $q->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('snapshot_pelamar')
                            ->whereColumn('snapshot_pelamar.formasi_kuota_id', 'formasi_kuota.id')
                            ->where('snapshot_pelamar.jumlah_ms', 0)
                            ->whereRaw('snapshot_pelamar.tanggal_snapshot = (SELECT MAX(tanggal_snapshot) FROM snapshot_pelamar sp2 WHERE sp2.formasi_kuota_id = formasi_kuota.id)');
                    });
                })
                ->count();

            $contoh = (clone $dasar)
                ->where('kartu_contoh', true)
                ->with(['instansi', 'musim', 'kuota'])
                ->limit(3)
                ->get();
        }

        $jurusanItems = Jurusan::query()
            ->orderBy('jenjang')
            ->orderBy('nama')
            ->get()
            ->map(fn (Jurusan $j) => [
                'value' => $j->slug,
                'label' => $j->nama,
                'jenjang' => $j->jenjang,
            ])
            ->all();

        return view('beranda', [
            'musim' => $musim,
            'statistik' => $statistik,
            'contoh' => $contoh,
            'jurusanItems' => $jurusanItems,
            'totalJurusan' => count($jurusanItems),
        ]);
    }
}
