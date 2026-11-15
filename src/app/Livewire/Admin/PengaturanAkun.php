<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Pengaturan Akun admin — versi minimal dari Settings bawaan starter kit
 * (15 Nov 2026): hanya Profil (nama + email) dan Kata Sandi. Appearance
 * (mode gelap ditunda per DESIGN.md) dan Delete account SENGAJA tidak
 * disertakan: menghapus akun admin mengunci panel, dan menghapus akun
 * peserta menghancurkan catatan pass yang sudah dibayar.
 */
#[Layout('components.layouts.admin')]
#[Title('Peta Formasi')]
class PengaturanAkun extends Component
{
    public string $nama = '';

    public string $email = '';

    public string $sandiSekarang = '';

    public string $sandiBaru = '';

    public string $sandiBaruKonfirmasi = '';

    public function mount(): void
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

    public function render()
    {
        return view('livewire.admin.pengaturan-akun');
    }
}
