<?php

namespace App\Livewire;

use App\Models\KodeAkses;
use App\Models\User;
use App\Models\UserPass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Redeem kode akses (FR-B2). Satu kode = satu akun (PRD §9): kode yang sudah
 * terpakai tidak bisa dipakai akun lain; kode juga tidak hangus bila akun
 * ternyata sudah punya pass aktif untuk musim yang sama.
 */
#[Layout('components.layouts.public')]
#[Title('Redeem Kode Akses — Peta Formasi')]
class RedeemForm extends Component
{
    public string $kode = '';

    public string $galat = '';

    /** @var array{tipe: string, musim: string, hingga: ?string}|null */
    public ?array $hasil = null;

    protected function rules(): array
    {
        return ['kode' => ['required', 'string', 'min:4', 'max:60']];
    }

    public function redeem(): void
    {
        $this->validate();
        $this->galat = '';
        $this->hasil = null;

        /** @var User $pengguna */
        $pengguna = Auth::user();

        $bersih = strtoupper(trim($this->kode));

        $kodeAkses = KodeAkses::query()->with('musim')->where('kode', $bersih)->first();

        if ($kodeAkses === null) {
            $this->galat = 'Kode tidak ditemukan. Periksa kembali kode dari halaman pembelian Lynk.id kamu — perhatikan huruf yang mirip seperti O dan 0.';
            return;
        }

        if ($kodeAkses->status === 'dicabut') {
            $this->galat = 'Kode ini sudah dicabut dan tidak berlaku lagi. Bila kamu merasa ini keliru, hubungi penjual dengan menyertakan bukti pembelian.';
            return;
        }

        if ($kodeAkses->status === 'terpakai') {
            $this->galat = $kodeAkses->user_id === $pengguna->id
                ? 'Kode ini sudah kamu redeem sebelumnya — pass-nya sudah ada di akunmu. Lihat di halaman Pass Saya.'
                : 'Kode ini sudah dipakai oleh akun lain. Satu kode hanya berlaku untuk satu akun dan tidak bisa dipindahkan.';
            return;
        }

        // Akun sudah punya pass aktif untuk musim ini: kode TIDAK hangus.
        if ($pengguna->punyaAksesMusim($kodeAkses->musim)) {
            $this->galat = 'Kamu sudah punya pass aktif untuk ' . $kodeAkses->musim->label . '. Kode ini tidak dipakai dan tetap utuh — pass kedua untuk musim yang sama tidak menambah apa pun.';
            return;
        }

        DB::transaction(function () use ($pengguna, $kodeAkses) {
            // Kunci baris supaya dua redeem bersamaan tidak sama-sama lolos.
            $terkunci = KodeAkses::query()->whereKey($kodeAkses->id)->lockForUpdate()->first();

            if ($terkunci->status !== 'belum_dipakai') {
                return;
            }

            $hingga = null;
            if ($terkunci->tipe_pass === 'strategi' && $terkunci->musim->pendaftaran_tutup) {
                // PRD §9: strategi berlaku sampai 30 hari setelah pendaftaran ditutup.
                $batasMusim = $terkunci->musim->pendaftaran_tutup->copy()->addDays(30);

                // Perlindungan: pass tidak boleh LAHIR sudah berakhir. Bila kode
                // di-redeem jauh setelah pendaftaran tutup (mis. kode strategi
                // untuk musim historis di data uji), masa berlaku dihitung minimal
                // 30 hari sejak aktivasi — tanggal PRD tetap menang bila lebih lama.
                $batasAktivasi = now()->addDays(30);
                $hingga = $batasMusim->greaterThan($batasAktivasi) ? $batasMusim : $batasAktivasi;
            }

            UserPass::create([
                'user_id' => $pengguna->id,
                'kode_akses_id' => $terkunci->id,
                // user_pass menyimpan salinan tipe & musim dari kode (denormalisasi skema Tahap 1).
                'tipe_pass' => $terkunci->tipe_pass,
                'musim_id' => $terkunci->musim_id,
                'aktif_dari' => now(),
                'aktif_hingga' => $hingga,
                'status' => 'aktif',
            ]);

            $terkunci->update([
                'status' => 'terpakai',
                'user_id' => $pengguna->id,
                'dipakai_pada' => now(),
            ]);

            $this->hasil = [
                'tipe' => $terkunci->tipe_pass === 'strategi' ? 'Pass Strategi' : 'Pass Peta Historis',
                'musim' => $terkunci->musim->label,
                'hingga' => $hingga?->translatedFormat('d F Y'),
            ];
        });
    }

    public function render()
    {
        return view('livewire.redeem-form');
    }
}
