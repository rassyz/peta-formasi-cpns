<?php

namespace App\Livewire\Akun;

use App\Livewire\Concerns\MengaturAkun;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Pengaturan akun sisi peserta di /akun/pengaturan (15 Nov 2026) —
 * pasangan halaman Pengaturan Akun admin: kartu Profil + Kata Sandi
 * yang sama, dibungkus layout publik. Logika formulirnya memakai trait
 * yang sama (App\Livewire\Concerns\MengaturAkun) agar aturan validasi
 * kedua halaman tidak berpisah jalan.
 */
#[Layout('components.layouts.public')]
#[Title('Peta Formasi')]
class Pengaturan extends Component
{
    use MengaturAkun;

    public function render()
    {
        return view('livewire.akun.pengaturan');
    }
}
