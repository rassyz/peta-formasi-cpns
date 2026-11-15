<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Logika bersama halaman Pengaturan akun — dipakai Admin\PengaturanAkun
 * (panel admin) dan Akun\Pengaturan (sisi peserta) sejak 15 Nov 2026,
 * supaya aturan profil & kata sandi tidak berpisah jalan:
 * - Profil: nama + email; email harus unik selain diri sendiri; email
 *   yang berubah status verifikasinya direset (perilaku kit).
 * - Kata Sandi: sandi sekarang wajib cocok (aturan current_password),
 *   sandi baru minimal 8 karakter + konfirmasi sama.
 * Hasil simpan dilaporkan lewat toast; galat isian tampil inline.
 * Appearance dan Delete account SENGAJA tidak ada (lihat PengaturanAkun).
 */
trait MengaturAkun
{
    public string $nama = '';

    public string $email = '';

    public string $sandiSekarang = '';

    public string $sandiBaru = '';

    public string $sandiBaruKonfirmasi = '';

    public function mountMengaturAkun(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->nama = $user->name;
        $this->email = $user->email;
    }

    public function simpanProfil(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $data = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ], [], [
            'nama' => 'nama',
            'email' => 'email',
        ]);

        $emailBerubah = $data['email'] !== $user->email;

        $user->forceFill([
            'name' => $data['nama'],
            'email' => $data['email'],
            // Alamat baru belum terverifikasi — sama seperti perilaku kit.
            'email_verified_at' => $emailBerubah ? null : $user->email_verified_at,
        ])->save();

        $this->dispatch('toast', tipe: 'sukses', pesan: $emailBerubah
            ? 'Profil tersimpan. Email login berubah — alamat baru berstatus belum terverifikasi.'
            : 'Profil tersimpan.');
    }

    public function simpanSandi(): void
    {
        $this->validate([
            'sandiSekarang' => ['required', 'current_password'],
            'sandiBaru' => ['required', 'string', 'min:8'],
            'sandiBaruKonfirmasi' => ['required', 'same:sandiBaru'],
        ], [
            'sandiSekarang.current_password' => 'Kata sandi sekarang tidak cocok.',
            'sandiBaru.min' => 'Kata sandi baru minimal 8 karakter.',
            'sandiBaruKonfirmasi.same' => 'Konfirmasi kata sandi baru tidak sama.',
        ], [
            'sandiSekarang' => 'kata sandi sekarang',
            'sandiBaru' => 'kata sandi baru',
            'sandiBaruKonfirmasi' => 'konfirmasi kata sandi baru',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->forceFill(['password' => Hash::make($this->sandiBaru)])->save();

        $this->reset(['sandiSekarang', 'sandiBaru', 'sandiBaruKonfirmasi']);

        $this->dispatch('toast', tipe: 'sukses', pesan: 'Kata sandi diperbarui. Gunakan kata sandi baru pada login berikutnya.');
    }
}
