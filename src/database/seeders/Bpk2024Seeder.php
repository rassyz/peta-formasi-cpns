<?php

namespace Database\Seeders;

use App\Models\ChecklistBerkas;
use App\Models\Formasi;
use App\Models\FormasiKuota;
use App\Models\Instansi;
use App\Models\Jurusan;
use App\Models\KodeAkses;
use App\Models\Musim;
use App\Models\RequestFormasi;
use App\Models\SnapshotPelamar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Data contoh pengembangan dari pola pengumuman CPNS BPK 2024 (subset ±15 formasi,
 * BUKAN impor penuh 154 — impor penuh adalah pekerjaan Tahap 5).
 *
 * Aturan instansi mengikuti pengumuman aslinya: usia 18–35 pada saat mendaftar,
 * IPK min 3,00, dan SKL secara eksplisit TIDAK diterima (R-05).
 * Angka pelamar bersifat contoh untuk menguji Radar Peluang selama pengembangan,
 * bukan statistik resmi. Aman dijalankan berulang (updateOrCreate).
 */
class Bpk2024Seeder extends Seeder
{
    public function run(): void
    {
        $musim = Musim::updateOrCreate(
            ['tahun' => 2024],
            [
                'label' => 'CPNS 2024',
                'tanggal_acuan_usia' => '2024-08-20',
                'tanggal_pengumuman' => '2024-08-19',
                'pendaftaran_mulai' => '2024-08-20',
                'pendaftaran_tutup' => '2024-09-10',
                'status' => 'historis',
            ]
        );

        $bpk = Instansi::updateOrCreate(
            ['slug' => 'badan-pemeriksa-keuangan'],
            [
                'nama' => 'Badan Pemeriksa Keuangan',
                'wilayah' => 'pusat',
                'situs_resmi' => 'https://www.bpk.go.id',
                'link_pengumuman' => null,
            ]
        );

        // ---- Master jurusan ----
        $jurusanDef = [
            ['S1', 'Akuntansi'], ['S1', 'Manajemen'], ['S1', 'Ilmu Hukum'], ['S1', 'Ekonomi'],
            ['S1', 'Teknik Informatika'], ['S1', 'Sistem Informasi'], ['S1', 'Statistika'],
            ['S1', 'Matematika'], ['S1', 'Teknik Komputer'], ['S1', 'Ilmu Komputer'],
            ['S1', 'Kearsipan'], ['S1', 'Ilmu Perpustakaan'], ['S1', 'Ilmu Administrasi'],
            ['S1', 'Ilmu Komunikasi'], ['S1', 'Hubungan Internasional'], ['S1', 'Administrasi Publik'],
            ['S1', 'Ilmu Politik'], ['S1', 'Farmasi'], ['S1', 'Kedokteran'], ['S1', 'Teknik Sipil'],
            ['S1', 'Teknik Pertambangan'], ['S1', 'Teknik Geologi'],
            ['Profesi', 'Apoteker'],
            ['D3', 'Administrasi'], ['D3', 'Akuntansi'],
        ];

        $jurusanMap = [];
        foreach ($jurusanDef as [$jenjang, $nama]) {
            $jurusan = Jurusan::updateOrCreate(
                ['slug' => Str::slug("{$jenjang} {$nama}")],
                ['jenjang' => $jenjang, 'nama' => $nama]
            );
            $jurusanMap["{$jenjang}|{$nama}"] = $jurusan->id;
        }

        // ---- Formasi (subset BPK 2024) ----
        // Format: jabatan, penempatan, [kunci jurusan], [jenis => kuota],
        //         snapshot umum [ms, total] (null = belum tersedia), opsi tambahan.
        $formasiDef = [
            ['Pemeriksa Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Akuntansi', 'S1|Manajemen', 'S1|Ilmu Hukum', 'S1|Ekonomi', 'S1|Teknik Informatika', 'S1|Sistem Informasi', 'S1|Statistika', 'S1|Matematika'],
                ['umum' => 40], [812, 1040], ['contoh' => true]],
            ['Pemeriksa Ahli Pertama', 'Perwakilan Provinsi Banten',
                ['S1|Akuntansi', 'S1|Manajemen', 'S1|Ilmu Hukum', 'S1|Ekonomi'],
                ['umum' => 6], [45, 61], []],
            ['Pranata Komputer Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Teknik Informatika', 'S1|Sistem Informasi', 'S1|Teknik Komputer', 'S1|Ilmu Komputer'],
                ['umum' => 5, 'cumlaude' => 1], [0, 3],
                ['contoh' => true, 'gaji' => [6500000, 8500000]]],
            ['Statistisi Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Statistika', 'S1|Matematika', 'S1|Teknik Informatika'],
                ['umum' => 3], [12, 15], ['contoh' => true]],
            ['Analis Hukum Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Ilmu Hukum'],
                ['umum' => 4], [98, 131], []],
            ['Arsiparis Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Kearsipan', 'S1|Ilmu Perpustakaan', 'S1|Ilmu Administrasi'],
                ['umum' => 2], null, ['akreditasi' => null]],
            ['Pranata Humas Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Ilmu Komunikasi', 'S1|Hubungan Internasional'],
                ['umum' => 2], [30, 44], []],
            ['Analis Kebijakan Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Administrasi Publik', 'S1|Ilmu Politik', 'S1|Ekonomi'],
                ['umum' => 3], [57, 73], []],
            ['Apoteker Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Farmasi', 'Profesi|Apoteker'],
                ['umum' => 1], [0, 0], []],
            ['Dokter Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Kedokteran'],
                ['umum' => 1], [2, 2], []],
            ['Pemeriksa Ahli Pertama', 'Perwakilan Provinsi Jawa Barat',
                ['S1|Akuntansi', 'S1|Ilmu Hukum', 'S1|Manajemen', 'S1|Teknik Sipil'],
                ['umum' => 8, 'disabilitas' => 1], [120, 150], []],
            ['Pemeriksa Ahli Pertama', 'Perwakilan Provinsi Sumatera Utara',
                ['S1|Akuntansi', 'S1|Ekonomi', 'S1|Ilmu Hukum'],
                ['umum' => 5], [64, 80], []],
            ['Penata Laksana Barang Terampil', 'Kantor Pusat — Jakarta',
                ['D3|Administrasi', 'D3|Akuntansi'],
                ['umum' => 4], [21, 29], ['akreditasi' => null]],
            ['Pengelola Pengadaan Barang/Jasa Ahli Pertama', 'Kantor Pusat — Jakarta',
                ['S1|Ilmu Hukum', 'S1|Ekonomi', 'S1|Teknik Sipil'],
                ['umum' => 2], [17, 22],
                ['syarat_khusus' => 'Diutamakan memiliki sertifikat kompetensi pengadaan barang/jasa pemerintah. Bersedia ditempatkan di seluruh wilayah kerja BPK.']],
            ['Pemeriksa Ahli Pertama', 'Perwakilan Provinsi Kalimantan Timur',
                ['S1|Akuntansi', 'S1|Teknik Pertambangan', 'S1|Teknik Geologi'],
                ['umum' => 4], [9, 11], []],
        ];

        $formasiByJabatan = [];

        foreach ($formasiDef as [$jabatan, $penempatan, $jurusanKeys, $kuotaDef, $snapshotDef, $opsi]) {
            $formasi = Formasi::updateOrCreate(
                [
                    'musim_id' => $musim->id,
                    'slug' => Str::slug("{$jabatan} {$penempatan}"),
                ],
                [
                    'instansi_id' => $bpk->id,
                    'jabatan' => $jabatan,
                    'penempatan' => $penempatan,
                    'usia_min' => 18,
                    'usia_maks' => 35,
                    'ipk_min' => 3.00,
                    'skl_diterima' => false,
                    'syarat_akreditasi' => $opsi['akreditasi'] ?? 'wajib',
                    'syarat_khusus' => $opsi['syarat_khusus'] ?? null,
                    'gaji_min' => $opsi['gaji'][0] ?? null,
                    'gaji_maks' => $opsi['gaji'][1] ?? null,
                    'sumber_gaji' => isset($opsi['gaji']) ? 'Data contoh dev — bukan angka resmi' : null,
                    'tanggal_verifikasi_gaji' => isset($opsi['gaji']) ? '2024-08-21' : null,
                    'sumber_pengumuman' => 'Pengumuman CPNS BPK 2024 (data contoh dev)',
                    'tanggal_verifikasi' => '2024-08-21',
                    'status_terbit' => 'terbit',
                    'kartu_contoh' => $opsi['contoh'] ?? false,
                ]
            );

            $formasi->jurusan()->sync(
                collect($jurusanKeys)->map(fn (string $key) => $jurusanMap[$key])->all()
            );

            foreach ($kuotaDef as $jenis => $jumlah) {
                $kuota = FormasiKuota::updateOrCreate(
                    ['formasi_id' => $formasi->id, 'jenis_kebutuhan' => $jenis],
                    ['jumlah' => $jumlah]
                );

                if ($snapshotDef !== null && $jenis === 'umum') {
                    SnapshotPelamar::updateOrCreate(
                        ['formasi_kuota_id' => $kuota->id, 'tanggal_snapshot' => '2024-09-15'],
                        [
                            'tipe' => 'historis',
                            'jumlah_ms' => $snapshotDef[0],
                            'jumlah_total' => $snapshotDef[1],
                            'sumber' => 'Statistik SSCASN 2024 — data contoh dev',
                        ]
                    );
                }
            }

            $formasiByJabatan[$jabatan] = $formasi;
        }

        // ---- Checklist default BPK (menempel di instansi) ----
        $checklistDef = [
            ['KTP', 'PDF/JPG', 1000],
            ['Pas Foto Terbaru', 'JPG', 200],
            ['Surat Lamaran', 'PDF', 1000],
            ['Ijazah', 'PDF', 1000],
            ['Transkrip Nilai', 'PDF', 1000],
        ];

        foreach ($checklistDef as $i => [$nama, $format, $maksKb]) {
            ChecklistBerkas::updateOrCreate(
                ['instansi_id' => $bpk->id, 'formasi_id' => null, 'nama_dokumen' => $nama],
                ['format' => $format, 'ukuran_maks_kb' => $maksKb, 'wajib' => true, 'urutan' => $i + 1]
            );
        }

        // ---- Contoh baris override di level formasi (Dokter: dokumen khusus) ----
        if (isset($formasiByJabatan['Dokter Ahli Pertama'])) {
            ChecklistBerkas::updateOrCreate(
                [
                    'instansi_id' => $bpk->id,
                    'formasi_id' => $formasiByJabatan['Dokter Ahli Pertama']->id,
                    'nama_dokumen' => 'Surat Tanda Registrasi (STR)',
                ],
                ['format' => 'PDF', 'ukuran_maks_kb' => 1000, 'wajib' => true, 'urutan' => 6]
            );
        }

        // ---- Kode akses contoh untuk pengembangan (redeem dibangun Tahap 4) ----
        foreach (['HIST-2024-DEMO1', 'HIST-2024-DEMO2'] as $kode) {
            KodeAkses::updateOrCreate(
                ['kode' => $kode],
                [
                    'tipe_pass' => 'historis',
                    'musim_id' => $musim->id,
                    'status' => 'belum_dipakai',
                    'batch' => 'dev-tahap-1',
                ]
            );
        }

        // ---- Contoh request formasi dari tamu ----
        RequestFormasi::firstOrCreate(
            [
                'user_id' => null,
                'instansi_dicari' => 'Kementerian Keuangan',
                'jabatan_dicari' => 'Pranata Komputer',
            ],
            [
                'musim_id' => $musim->id,
                'catatan' => 'Data contoh dev.',
                'status' => 'menunggu',
            ]
        );
    }
}
