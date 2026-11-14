<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin: daftar peserta READ-ONLY untuk keperluan dukungan pembeli —
 * telusur akun dari nama/email, lalu lihat pass yang dipegang beserta kode
 * redeem-nya. Halaman ini sengaja TANPA aksi ubah/hapus apa pun.
 */
#[Layout('components.layouts.admin')]
#[Title('Peserta — Admin Peta Formasi')]
class PesertaManager extends Component
{
    use WithPagination;

    public string $cari = '';

    /** '' = semua | 'aktif' = punya pass aktif | 'tanpa' = belum punya pass */
    public string $saringPass = '';

    /** Id peserta yang panel detailnya sedang dibuka (satu per satu). */
    public ?int $terbukaId = null;

    public function updated(string $properti): void
    {
        if (in_array($properti, ['cari', 'saringPass'], true)) {
            $this->resetPage();
        }
    }

    public function toggleDetail(int $id): void
    {
        $this->terbukaId = $this->terbukaId === $id ? null : $id;
    }

    public function render()
    {
        $pengguna = User::query()
            ->where('role', 'peserta')
            ->when($this->cari !== '', function ($q) {
                $kata = '%' . trim($this->cari) . '%';
                $q->where(fn ($sub) => $sub->where('name', 'like', $kata)->orWhere('email', 'like', $kata));
            })
            ->when($this->saringPass === 'aktif', fn ($q) => $q->whereHas('passes', fn ($p) => $p->aktif()))
            ->when($this->saringPass === 'tanpa', fn ($q) => $q->whereDoesntHave('passes'))
            ->with(['passes' => fn ($p) => $p->with(['musim', 'kodeAkses'])->orderByDesc('aktif_dari')])
            ->withCount('passes')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.admin.peserta-manager', [
            'pengguna' => $pengguna,
        ]);
    }
}
