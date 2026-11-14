<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Unduh template CSV impor formasi (kolom sesuai PRD §10 & ImporFormasi). */
class TemplateImporController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        $header = [
            'jabatan', 'penempatan', 'kualifikasi', 'kuota_umum', 'kuota_cumlaude',
            'kuota_disabilitas', 'kuota_lainnya', 'usia_min', 'usia_maks', 'ipk_min',
            'skl_diterima', 'syarat_akreditasi', 'syarat_khusus', 'gaji_min', 'gaji_maks',
            'sumber_gaji', 'tanggal_verifikasi_gaji', 'sumber_pengumuman',
            'tanggal_verifikasi', 'kartu_contoh',
        ];

        $contoh = [
            'CONTOH HAPUS BARIS INI — Analis Data Ahli Pertama',
            'Kantor Pusat — Jakarta',
            'S1 Statistika|S1 Matematika',
            '5', '1', '0', 'papua:1',
            '18', '35', '3.00', 'tidak', '', '',
            '6500000', '8500000',
            'Pengumuman resmi instansi', '2026-01-10',
            'Pengumuman Seleksi CPNS Instansi 2026', '2026-01-10',
            'tidak',
        ];

        return response()->streamDownload(function () use ($header, $contoh) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $header);
            fputcsv($out, $contoh);
            fclose($out);
        }, 'template-impor-formasi.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
