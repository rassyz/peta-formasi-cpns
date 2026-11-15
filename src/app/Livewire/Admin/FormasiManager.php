<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\KonfirmasiAksi;
use App\Models\Formasi;
use App\Models\Instansi;
use App\Models\Musim;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin: kelola formasi hasil impor — terbitkan/tarik, tandai kartu contoh
 * (maks 3 per jurusan, FR-A8), hapus draft, dan input snapshot pelamar
 * manual (Modul B; jembatan sebelum Radar live di Tahap 7).
 */
#[Layout('components.layouts.admin')]
#[Title('Kelola Formasi — Admin Peta Formasi')]
class FormasiManager extends Component
{
    use WithPagination;
    use KonfirmasiAksi;

    public ?int $musimId = null;

    public string $instansiId = '';

    public string $saringStatus = '';

    /** Pencarian teks nama formasi (jabatan/penempatan/instansi) pada daftar. */
    public string $cariFormasi = '';


    // --- Panel snapshot manual ---
    public string $snapshotFormasiId = '';

    public string $snapshotJenis = 'umum';

    public string $snapshotTanggal = '';

    public ?int $snapshotMs = null;

    public ?int $snapshotTotal = null;

    public string $snapshotTipe = 'historis';

    public string $snapshotSumber = '';

    public function mount(): void
    {
        $this->musimId = Musim::defaultTampil()?->id;
        $this->snapshotTanggal = now()->format('Y-m-d');
    }

    public function updated(string $properti): void
    {
        if (in_array($properti, ['musimId', 'instansiId', 'saringStatus', 'cariFormasi'], true)) {
            $this->resetPage();
        }
    }

    public function terbitkan(int $id): void
    {
        Formasi::query()->whereKey($id)->update(['status_terbit' => 'terbit']);
        $this->dispatch('toast', tipe: 'sukses', pesan: 'Formasi diterbitkan.');
    }

    public function tarik(int $id): void
    {
        Formasi::query()->whereKey($id)->update(['status_terbit' => 'draft']);
        $this->dispatch('toast', tipe: 'sukses', pesan: 'Formasi ditarik kembali ke draft.');
    }

    public function hapus(int $id): void
    {
        $formasi = Formasi::query()->findOrFail($id);
        if ($formasi->status_terbit !== 'draft') {
            $this->dispatch('toast', tipe: 'peringatan', pesan: 'Hanya formasi draft yang bisa dihapus. Tarik dulu bila sudah terbit.');
            return;
        }
        $formasi->delete();
        $this->dispatch('toast', tipe: 'sukses', pesan: 'Formasi draft dihapus.');
    }

    public function terbitkanSemuaDraft(): void
    {
        $jumlah = Formasi::query()
            ->where('musim_id', $this->musimId)
            ->when($this->instansiId !== '', fn($q) => $q->where('instansi_id', (int) $this->instansiId))
            ->where('status_terbit', 'draft')
            ->update(['status_terbit' => 'terbit']);
        $this->dispatch('toast', tipe: 'sukses', pesan: $jumlah . ' formasi draft diterbitkan.');
    }

    public function toggleContoh(int $id): void
    {
        $formasi = Formasi::query()->with('jurusan')->findOrFail($id);

        if ($formasi->kartu_contoh) {
            $formasi->update(['kartu_contoh' => false]);
            $this->dispatch('toast', tipe: 'sukses', pesan: 'Tanda kartu contoh dicabut dari "' . $formasi->jabatan . '".');
            return;
        }

        // FR-A8: maks 3 kartu contoh per jurusan pada musim yang sama.
        foreach ($formasi->jurusan as $jurusan) {
            $jumlah = Formasi::query()
                ->where('musim_id', $formasi->musim_id)
                ->where('kartu_contoh', true)
                ->whereKeyNot($formasi->id)
                ->whereHas('jurusan', fn($q) => $q->where('jurusan.id', $jurusan->id))
                ->count();
            if ($jumlah >= 3) {
                $this->dispatch('toast', tipe: 'peringatan', pesan: 'Jurusan ' . $jurusan->labelLengkap() . ' sudah punya 3 kartu contoh pada musim ini (batas FR-A8). Cabut salah satu dulu.');
                return;
            }
        }

        $formasi->update(['kartu_contoh' => true]);
        $this->dispatch('toast', tipe: 'sukses', pesan: '"' . $formasi->jabatan . '" ditandai sebagai kartu contoh — detailnya terbuka untuk semua pengunjung.');
    }

    public function simpanSnapshot(): void
    {
        $this->validate([
            'snapshotFormasiId' => ['required', 'exists:formasi,id'],
            'snapshotJenis' => ['required', 'string'],
            'snapshotTanggal' => ['required', 'date'],
            'snapshotMs' => ['required', 'integer', 'min:0'],
            'snapshotTotal' => ['nullable', 'integer', 'min:0'],
            'snapshotTipe' => ['required', 'in:historis,live'],
            'snapshotSumber' => ['nullable', 'string', 'max:160'],
        ]);

        $formasi = Formasi::query()->with('kuota')->findOrFail((int) $this->snapshotFormasiId);
        $kuota = $formasi->kuota->firstWhere('jenis_kebutuhan', $this->snapshotJenis);

        if ($kuota === null) {
            $this->dispatch('toast', tipe: 'peringatan', pesan: 'Formasi itu tidak punya kuota jenis "' . $this->snapshotJenis . '".');
            return;
        }

        $kuota->snapshot()->create([
            'tipe' => $this->snapshotTipe,
            'jumlah_ms' => $this->snapshotMs,
            'jumlah_total' => $this->snapshotTotal,
            'tanggal_snapshot' => $this->snapshotTanggal,
            'sumber' => $this->snapshotSumber !== '' ? $this->snapshotSumber : null,
        ]);

        $this->dispatch('toast', tipe: 'sukses', pesan: 'Snapshot tersimpan untuk ' . $formasi->jabatan . ' (' . $this->snapshotJenis . ').');
    }

    public function render()
    {
        $daftar = Formasi::query()
            ->where('musim_id', $this->musimId)
            ->when(trim($this->cariFormasi) !== '', function ($q) {
                $kata = '%' . trim($this->cariFormasi) . '%';
                $q->where(function ($sub) use ($kata) {
                    $sub->where('formasi.jabatan', 'like', $kata)
                        ->orWhere('formasi.penempatan', 'like', $kata)
                        ->orWhereHas('instansi', fn($i) => $i->where('nama', 'like', $kata));
                });
            })
            ->when($this->instansiId !== '', fn($q) => $q->where('instansi_id', (int) $this->instansiId))
            ->when($this->saringStatus !== '', fn($q) => $q->where('status_terbit', $this->saringStatus))
            ->with(['instansi'])
            ->withSum('kuota as total_kuota', 'jumlah')
            ->withCount('jurusan')
            ->orderBy('jabatan')
            ->paginate(15);

        $formasiUntukSnapshot = Formasi::query()
            ->where('musim_id', $this->musimId)
            ->orderBy('jabatan')
            ->get(['id', 'jabatan', 'penempatan']);

        $snapshotTerbaru = collect();
        if ($this->snapshotFormasiId !== '') {
            $snapshotTerbaru = \App\Models\SnapshotPelamar::query()
                ->whereHas('formasiKuota', fn($q) => $q->where('formasi_id', (int) $this->snapshotFormasiId))
                ->with('formasiKuota')
                ->latest('tanggal_snapshot')
                ->limit(8)
                ->get();
        }

        return view('livewire.admin.formasi-manager', [
            'musimList' => Musim::query()->orderByDesc('tahun')->get(),
            'instansiList' => Instansi::query()->orderBy('nama')->get(),
            'daftar' => $daftar,
            'formasiUntukSnapshot' => $formasiUntukSnapshot,
            'snapshotTerbaru' => $snapshotTerbaru,
        ]);
    }
}
