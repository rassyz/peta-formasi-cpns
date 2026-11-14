<?php

namespace App\Services;

use App\Models\Formasi;
use App\Models\Jurusan;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;

/**
 * Mesin aturan Cek Kelayakan (PRD Bagian 6, R-01 s.d. R-08).
 *
 * Service ini murni menilai data terstruktur. Ia tidak menyimpan profil,
 * tidak mengubah data formasi, dan tidak pernah menyatakan peserta pasti
 * diterima — hasil tertingginya hanya "Lolos Syarat" formal.
 */
class CekKelayakan
{
    /**
     * @param  array{
     *     tanggal_lahir: string,
     *     ipk: float,
     *     dokumen: string,
     *     akreditasi: string,
     *     jenis_kebutuhan: string
     * }  $profil Profil sesi dari FR-B1.
     * @return array{
     *     status: string,
     *     label: string,
     *     alasan: ?string,
     *     aturan: array<int, array{kode: string, label: string, status: string, pesan: string}>
     * }
     */
    public function nilai(Formasi $formasi, Jurusan $jurusan, array $profil): array
    {
        $formasi->loadMissing(['musim', 'jurusan', 'kuota']);

        $aturan = [];
        $adaGagal = false;
        $adaPerlu = false;

        $tambah = function (
            string $kode,
            string $label,
            string $status,
            string $pesan
        ) use (&$aturan, &$adaGagal, &$adaPerlu): void {
            $aturan[] = [
                'kode' => $kode,
                'label' => $label,
                'status' => $status,
                'pesan' => $pesan,
            ];

            if ($status === 'gagal') {
                $adaGagal = true;
            }

            if ($status === 'perlu') {
                $adaPerlu = true;
            }
        };

        // R-01 — usia selalu dihitung ke tanggal acuan musim yang dinilai.
        $acuan = $formasi->musim?->tanggal_acuan_usia;
        $lahir = null;

        if ($acuan === null) {
            $tambah(
                'R-01',
                'Tanggal acuan usia',
                'perlu',
                'Musim ini belum memiliki tanggal acuan usia, jadi usia belum dapat dinilai secara pasti.'
            );
        } else {
            $lahir = CarbonImmutable::parse($profil['tanggal_lahir'])->startOfDay();
            $acuanImmutable = CarbonImmutable::instance($acuan)->startOfDay();

            $tambah(
                'R-01',
                'Tanggal acuan usia',
                'lolos',
                'Usia dihitung pada ' . $acuanImmutable->translatedFormat('d F Y')
                    . ', tanggal acuan ' . $formasi->musim->label . '.'
            );

            // R-02 — batas maksimum mengikuti bunyi PRD secara persis:
            // 35 tahun 0 bulan 0 hari. Karena itu lahir tepat pada
            // (acuan - usia maksimum) masih lolos; satu hari lebih tua gagal.
            $usiaTeks = $this->formatSelisihUsia($lahir->diff($acuanImmutable));
            $gagalUsia = null;

            if ($formasi->usia_min !== null) {
                $tanggalGenapMin = $lahir->addYears($formasi->usia_min);
                if ($tanggalGenapMin->greaterThan($acuanImmutable)) {
                    $gagalUsia = 'Usia kamu ' . $usiaTeks . ' pada tanggal acuan — di bawah minimum '
                        . $formasi->usia_min . ' tahun.';
                }
            }

            if ($gagalUsia === null && $formasi->usia_maks !== null) {
                $batasLahirMaks = $acuanImmutable->subYears($formasi->usia_maks);
                if ($lahir->lessThan($batasLahirMaks)) {
                    $gagalUsia = 'Usia kamu ' . $usiaTeks . ' pada tanggal acuan — melebihi maksimum '
                        . $formasi->usia_maks . ' tahun 0 bulan 0 hari.';
                }
            }

            if ($gagalUsia !== null) {
                $tambah('R-02', 'Usia', 'gagal', $gagalUsia);
            } elseif ($formasi->usia_min === null && $formasi->usia_maks === null) {
                $tambah(
                    'R-02',
                    'Usia',
                    'lolos',
                    'Formasi ini tidak menetapkan batas usia terstruktur. Usia kamu pada tanggal acuan: '
                        . $usiaTeks . '.'
                );
            } else {
                $rentang = trim(
                    ($formasi->usia_min !== null ? 'minimum ' . $formasi->usia_min . ' tahun' : '')
                    . ($formasi->usia_min !== null && $formasi->usia_maks !== null ? ', ' : '')
                    . ($formasi->usia_maks !== null ? 'maksimum ' . $formasi->usia_maks . ' tahun' : '')
                );
                $tambah(
                    'R-02',
                    'Usia',
                    'lolos',
                    'Usia kamu ' . $usiaTeks . ' pada tanggal acuan — memenuhi syarat ' . $rentang . '.'
                );
            }
        }

        // R-03 — daftar pivot adalah satu-satunya pencocokan jurusan yang sah.
        $jurusanDiterima = $formasi->jurusan->contains('id', $jurusan->id);
        $tambah(
            'R-03',
            'Jenjang & jurusan',
            $jurusanDiterima ? 'lolos' : 'gagal',
            $jurusanDiterima
                ? $jurusan->labelLengkap() . ' tercantum dalam kualifikasi formasi ini.'
                : 'Kualifikasi formasi ini tidak mencakup ' . $jurusan->labelLengkap() . '.'
        );

        // R-04 — IPK hanya dinilai bila formasi menetapkan minimum.
        if ($formasi->ipk_min === null) {
            $tambah(
                'R-04',
                'IPK',
                'lolos',
                'Formasi ini tidak menetapkan IPK minimum terstruktur. IPK kamu '
                    . $this->formatDesimal((float) $profil['ipk']) . '.'
            );
        } elseif ((float) $profil['ipk'] + 0.000001 >= (float) $formasi->ipk_min) {
            $tambah(
                'R-04',
                'IPK',
                'lolos',
                'IPK kamu ' . $this->formatDesimal((float) $profil['ipk'])
                    . ' memenuhi minimum ' . $this->formatDesimal((float) $formasi->ipk_min) . '.'
            );
        } else {
            $tambah(
                'R-04',
                'IPK',
                'gagal',
                'IPK kamu ' . $this->formatDesimal((float) $profil['ipk'])
                    . ' di bawah minimum ' . $this->formatDesimal((float) $formasi->ipk_min) . '.'
            );
        }

        // R-05 — SKL hanya menggugurkan bila formasi secara eksplisit menolaknya.
        if ($profil['dokumen'] === 'skl' && ! $formasi->skl_diterima) {
            $tambah(
                'R-05',
                'Ijazah / SKL',
                'gagal',
                'Kamu baru memiliki SKL, sedangkan formasi ini tidak menerima SKL — wajib sudah memiliki ijazah.'
            );
        } elseif ($profil['dokumen'] === 'skl') {
            $tambah(
                'R-05',
                'Ijazah / SKL',
                'lolos',
                'Formasi ini menerima SKL, sesuai status dokumen kamu saat ini.'
            );
        } else {
            $tambah(
                'R-05',
                'Ijazah / SKL',
                'lolos',
                'Kamu sudah memiliki ijazah, sesuai syarat dokumen formasi ini.'
            );
        }

        // R-06 — nilai selain kosong/"tidak disyaratkan" diperlakukan sebagai syarat.
        $syaratAkreditasi = trim((string) ($formasi->syarat_akreditasi ?? ''));
        $akreditasiDisyaratkan = $syaratAkreditasi !== ''
            && ! in_array(strtolower($syaratAkreditasi), ['tidak', 'tidak disyaratkan', '-'], true);

        if (! $akreditasiDisyaratkan) {
            $tambah(
                'R-06',
                'Akreditasi prodi',
                'lolos',
                'Formasi ini tidak mensyaratkan akreditasi prodi secara terstruktur.'
            );
        } elseif ($profil['akreditasi'] === 'terakreditasi') {
            $tambah(
                'R-06',
                'Akreditasi prodi',
                'lolos',
                'Prodi kamu terakreditasi saat lulus, sesuai syarat formasi ini'
                    . ($syaratAkreditasi !== 'wajib' ? ' (' . $syaratAkreditasi . ')' : '') . '.'
            );
        } elseif ($profil['akreditasi'] === 'tidak') {
            $tambah(
                'R-06',
                'Akreditasi prodi',
                'gagal',
                'Formasi ini mensyaratkan prodi terakreditasi saat lulus, sedangkan prodi kamu tidak terakreditasi.'
            );
        } else {
            $tambah(
                'R-06',
                'Akreditasi prodi',
                'perlu',
                'Formasi ini mensyaratkan prodi terakreditasi, tetapi kamu menjawab tidak tahu — cek akreditasi prodi saat kelulusanmu.'
            );
        }

        // R-07 — jenis kebutuhan yang dipilih harus benar-benar dibuka formasinya.
        $jenis = (string) $profil['jenis_kebutuhan'];
        $kuotaJenis = $formasi->kuota->firstWhere('jenis_kebutuhan', $jenis);
        $kuotaTersedia = $kuotaJenis !== null && (int) $kuotaJenis->jumlah > 0;

        $tambah(
            'R-07',
            'Jenis kebutuhan',
            $kuotaTersedia ? 'lolos' : 'gagal',
            $kuotaTersedia
                ? 'Formasi ini membuka kuota ' . ucfirst($jenis) . ' sebanyak '
                    . number_format((int) $kuotaJenis->jumlah, 0, ',', '.') . '.'
                : 'Formasi ini tidak membuka kuota ' . ucfirst($jenis) . '.'
        );

        // R-08 — selalu cek mandiri dan tidak pernah mengubah status otomatis.
        $tambah(
            'R-08',
            'Syarat khusus',
            'info',
            $formasi->syarat_khusus
                ? 'Ada syarat khusus dari pengumuman yang harus kamu nilai sendiri: "'
                    . $formasi->syarat_khusus . '"'
                : 'Tidak ada syarat khusus terpisah yang tercatat untuk formasi ini. Tetap baca pengumuman resmi secara utuh.'
        );

        $status = $adaGagal ? 'gagal' : ($adaPerlu ? 'perlu' : 'lolos');

        // Alasan kartu dibuat pendek: aturan gagal/perlu pertama ditambah
        // penanda bila masih ada alasan lain. Detail menampilkan semuanya.
        $alasanUtama = collect($aturan)
            ->whereIn('status', $status === 'gagal' ? ['gagal'] : ['perlu'])
            ->pluck('pesan')
            ->all();

        $alasan = null;
        if ($alasanUtama !== []) {
            $alasan = $alasanUtama[0];
            if (count($alasanUtama) > 1) {
                $alasan .= ' (+' . (count($alasanUtama) - 1) . ' alasan lain — buka detail)';
            }
        }

        return [
            'status' => $status,
            'label' => match ($status) {
                'lolos' => 'Lolos Syarat',
                'perlu' => 'Perlu Dicek',
                default => 'Tidak Memenuhi',
            },
            'alasan' => $alasan,
            'aturan' => $aturan,
        ];
    }

    private function formatDesimal(float $nilai): string
    {
        return number_format($nilai, 2, ',', '.');
    }

    private function formatSelisihUsia(CarbonInterval $selisih): string
    {
        $bagian = [];

        if ($selisih->y > 0) {
            $bagian[] = $selisih->y . ' tahun';
        }
        if ($selisih->m > 0) {
            $bagian[] = $selisih->m . ' bulan';
        }
        if ($selisih->d > 0) {
            $bagian[] = $selisih->d . ' hari';
        }

        return $bagian === [] ? '0 hari' : implode(' ', $bagian);
    }
}
