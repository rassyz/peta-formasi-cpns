<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\KonfirmasiAksi;
use App\Models\KodeAkses;
use App\Models\Musim;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin: buat batch kode akses + daftar & cabut kode (FR-B3).
 * Kode inilah yang dijual lewat Lynk.id lalu di-redeem peserta (FR-B2).
 */
#[Layout('components.layouts.admin')]
#[Title('Kode Akses — Admin Peta Formasi')]
class KodeAksesManager extends Component
{
    use WithPagination;
    use KonfirmasiAksi;

    public ?int $musimId = null;

    public string $tipe = 'historis'; // historis | strategi

    public int $jumlah = 10;

    public string $batch = '';

    public string $saringStatus = '';


    protected function rules(): array
    {
        return [
            'musimId' => ['required', 'exists:musim,id'],
            'tipe' => ['required', 'in:historis,strategi'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:200'],
            'batch' => ['nullable', 'string', 'max:60'],
        ];
    }

    public function mount(): void
    {
        $this->musimId = Musim::defaultTampil()?->id;
    }

    public function buatBatch(): void
    {
        $this->validate();

        $musim = Musim::findOrFail($this->musimId);
        $prefix = ($this->tipe === 'strategi' ? 'STRG' : 'HIST') . '-' . $musim->tahun . '-';
        $namaBatch = $this->batch !== '' ? $this->batch : 'batch-' . now()->format('Ymd-His');

        $dibuat = 0;
        while ($dibuat < $this->jumlah) {
            $kode = $prefix . Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4));
            if (KodeAkses::query()->where('kode', $kode)->exists()) {
                continue;
            }
            KodeAkses::create([
                'musim_id' => $musim->id,
                'kode' => $kode,
                'tipe_pass' => $this->tipe,
                'status' => 'belum_dipakai',
                'batch' => $namaBatch,
            ]);
            $dibuat++;
        }

        $this->dispatch('toast', tipe: 'sukses', pesan: $dibuat . ' kode ' . ($this->tipe === 'strategi' ? 'Pass Strategi' : 'Pass Peta Historis')
            . ' untuk ' . $musim->label . ' dibuat pada batch "' . $namaBatch . '". Salin dari tabel di bawah untuk dikirim ke pembeli Lynk.id.');
        $this->resetPage();
    }

    public function cabut(int $id): void
    {
        $kode = KodeAkses::query()->findOrFail($id);

        if ($kode->status === 'belum_dipakai') {
            $kode->update(['status' => 'dicabut']);
            $this->dispatch('toast', tipe: 'sukses', pesan: 'Kode ' . $kode->kode . ' dicabut — tidak bisa di-redeem lagi.');
        }
    }

    public function updated(string $properti): void
    {
        if ($properti === 'saringStatus') {
            $this->resetPage();
        }
    }

    public function render()
    {
        $daftar = KodeAkses::query()
            ->with(['musim', 'user'])
            ->when($this->saringStatus !== '', fn($q) => $q->where('status', $this->saringStatus))
            ->latest('id')
            ->paginate(15);

        return view('livewire.admin.kode-akses-manager', [
            'musimList' => Musim::query()->orderByDesc('tahun')->get(),
            'daftar' => $daftar,
        ]);
    }
}
