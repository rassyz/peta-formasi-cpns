<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\MengaturAkun;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Pengaturan Akun admin — versi minimal dari Settings bawaan starter kit
 * (15 Nov 2026): hanya Profil (nama + email) dan Kata Sandi. Appearance
 * (mode gelap ditunda per DESIGN.md) dan Delete account SENGAJA tidak
 * disertakan: menghapus akun admin mengunci panel, dan menghapus akun
 * peserta menghancurkan catatan pass yang sudah dibayar.
 *
 * Logika formulirnya hidup di trait App\Livewire\Concerns\MengaturAkun,
 * dibagi dengan halaman Pengaturan sisi peserta (Akun\Pengaturan).
 */
#[Layout('components.layouts.admin')]
#[Title('Peta Formasi')]
class PengaturanAkun extends Component
{
    use MengaturAkun;

    public function render()
    {
        return view('livewire.admin.pengaturan-akun');
    }
}
