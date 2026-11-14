<?php

namespace App\Livewire;

use App\Models\Musim;
use App\Models\RequestFormasi;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;


/** Form request formasi (FR-A7) — tamu boleh mengirim, akun tidak wajib. */
class RequestFormasiForm extends Component
{
    public ?int $musimId = null;

    public string $instansiDicari = '';

    public string $jabatanDicari = '';

    public string $catatan = '';

    public bool $terkirim = false;

    public function mount(?int $musimId = null): void
    {
        $this->musimId = $musimId ?? Musim::defaultTampil()?->id;
    }

    protected function rules(): array
    {
        return [
            'instansiDicari' => ['required', 'string', 'min:3', 'max:120'],
            'jabatanDicari' => ['required', 'string', 'min:3', 'max:120'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function simpan(): void
    {
        $this->validate();

        RequestFormasi::create([
            'musim_id' => $this->musimId,
            'user_id' => Auth::id(),
            'instansi_dicari' => $this->instansiDicari,
            'jabatan_dicari' => $this->jabatanDicari,
            'catatan' => $this->catatan !== '' ? $this->catatan : null,
            'status' => 'menunggu',
        ]);

        $this->reset(['instansiDicari', 'jabatanDicari', 'catatan']);
        $this->terkirim = true;
    }

    public function render()
    {
        return view('livewire.request-formasi-form');
    }
}
