<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Unduh template CSV impor snapshot pelamar (kolom sesuai ImporSnapshot). */
class TemplateSnapshotController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        $header = [
            'jabatan', 'penempatan', 'jenis_kebutuhan', 'tanggal_snapshot', 'tipe',
            'jumlah_ms', 'jumlah_total', 'sumber', 'slug',
        ];

        $contoh = [
            'CONTOH HAPUS BARIS INI — Pemeriksa',
            'Kantor Pusat',
            'umum',
            '2024-09-17',
            'historis',
            '812',
            '950',
            'Pengumuman hasil seleksi administrasi instansi',
            '',
        ];

        return response()->streamDownload(function () use ($header, $contoh) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            fputcsv($out, $contoh);
            fclose($out);
        }, 'template-impor-snapshot.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
