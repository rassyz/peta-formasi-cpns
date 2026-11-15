<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\KonfirmasiAksi;
use App\Models\ChecklistBerkas;
use App\Models\Instansi;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Kelola Instansi (15 Nov 2026) — menutup celah lama: instansi sebelumnya
 * hanya bisa dibuat lewat seeder/tinker, padahal ia prasyarat Impor
 * Formasi. Halaman ini mengelola master instansi (nama, slug, wilayah
 * pusat/daerah, situs resmi, link pengumuman) beserta CHECKLIST BERKAS
 * DEFAULT-nya (baris formasi_id null yang diwarisi semua formasi
 * instansi itu, PRD §7.2).
 *
 * Kaidah hapus mengikuti doktrin data beredar: instansi yang sudah
 * memiliki formasi TIDAK bisa dihapus (tombolnya bahkan tidak dirender);
 * instansi kosong boleh dihapus lewat modal konfirmasi, dan checklist
 * defaultnya ikut terhapus oleh cascade FK.
 */
#[Layout('components.layouts.admin')]
#[Title('Peta Formasi')]
class InstansiManager extends Component
{
    use KonfirmasiAksi;

    // Formulir instansi
    public ?int $editId = null;

    public string $nama = '';

    public string $slug = '';

    public string $wilayah = 'pusat';

    public string $situsResmi = '';

    public string $linkPengumuman = '';

    // Editor checklist default
    public ?int $checklistUntukId = null;

    public string $dokumenBaru = '';

    public string $formatBaru = '';

    public string $ukuranBaru = '';

    public function edit(int $id): void
    {
        $instansi = Instansi::findOrFail($id);
        $this->editId = $instansi->id;
        $this->nama = $instansi->nama;
        $this->slug = $instansi->slug;
        $this->wilayah = $instansi->wilayah;
        $this->situsResmi = (string) $instansi->situs_resmi;
        $this->linkPengumuman = (string) $instansi->link_pengumuman;
        $this->resetValidation();
    }

    public function batalEdit(): void
    {
        $this->reset(['editId', 'nama', 'slug', 'situsResmi', 'linkPengumuman']);
        $this->wilayah = 'pusat';
        $this->resetValidation();
    }

    public function simpan(): void
    {
        // Slug kosong dibuat dari nama; slug isian pun dinormalkan.
        $this->slug = Str::slug($this->slug !== '' ? $this->slug : $this->nama);

        $data = $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('instansi', 'slug')->ignore($this->editId)],
            'wilayah' => ['required', Rule::in(['pusat', 'daerah'])],
            'situsResmi' => ['nullable', 'url', 'max:255'],
            'linkPengumuman' => ['nullable', 'url', 'max:255'],
        ], [], [
            'nama' => 'nama instansi',
            'slug' => 'slug',
            'wilayah' => 'wilayah',
            'situsResmi' => 'situs resmi',
            'linkPengumuman' => 'link pengumuman',
        ]);

        $atribut = [
            'nama' => $data['nama'],
            'slug' => $data['slug'],
            'wilayah' => $data['wilayah'],
            'situs_resmi' => $data['situsResmi'] ?: null,
            'link_pengumuman' => $data['linkPengumuman'] ?: null,
        ];

        if ($this->editId) {
            Instansi::findOrFail($this->editId)->update($atribut);
            $this->dispatch('toast', tipe: 'sukses', pesan: 'Instansi diperbarui.');
        } else {
            Instansi::create($atribut);
            $this->dispatch('toast', tipe: 'sukses', pesan: 'Instansi baru ditambahkan — sudah bisa dipilih di Impor Formasi.');
        }

        $this->batalEdit();
    }

    public function hapus(int $id): void
    {
        $instansi = Instansi::withCount('formasi')->findOrFail($id);

        if ($instansi->formasi_count > 0) {
            $this->dispatch('toast', tipe: 'galat', pesan: 'Instansi ini sudah memiliki formasi dan tidak bisa dihapus.');

            return;
        }

        $nama = $instansi->nama;
        $instansi->delete(); // checklist default ikut terhapus (cascade FK)

        if ($this->checklistUntukId === $id) {
            $this->checklistUntukId = null;
        }
        if ($this->editId === $id) {
            $this->batalEdit();
        }

        $this->dispatch('toast', tipe: 'sukses', pesan: "Instansi {$nama} dihapus.");
    }

    public function toggleChecklist(int $id): void
    {
        $this->checklistUntukId = $this->checklistUntukId === $id ? null : $id;
        $this->reset(['dokumenBaru', 'formatBaru', 'ukuranBaru']);
        $this->resetValidation();
    }

    public function tambahChecklist(int $instansiId): void
    {
        $data = $this->validate([
            'dokumenBaru' => ['required', 'string', 'max:255'],
            'formatBaru' => ['nullable', 'string', 'max:50'],
            'ukuranBaru' => ['nullable', 'integer', 'min:1', 'max:102400'],
        ], [], [
            'dokumenBaru' => 'nama dokumen',
            'formatBaru' => 'format',
            'ukuranBaru' => 'ukuran maks',
        ]);

        $urutan = (int) (ChecklistBerkas::where('instansi_id', $instansiId)->whereNull('formasi_id')->max('urutan') ?? 0) + 1;

        ChecklistBerkas::create([
            'instansi_id' => $instansiId,
            'formasi_id' => null,
            'nama_dokumen' => $data['dokumenBaru'],
            'format' => $data['formatBaru'] ? Str::upper($data['formatBaru']) : null,
            'ukuran_maks_kb' => $data['ukuranBaru'] ?: null,
            'wajib' => true,
            'urutan' => $urutan,
        ]);

        $this->reset(['dokumenBaru', 'formatBaru', 'ukuranBaru']);
        $this->dispatch('toast', tipe: 'sukses', pesan: 'Dokumen ditambahkan ke checklist default.');
    }

    public function hapusChecklist(int $id): void
    {
        // Hanya baris DEFAULT (formasi_id null) yang dikelola halaman ini;
        // baris override per formasi tidak disentuh.
        $baris = ChecklistBerkas::whereNull('formasi_id')->findOrFail($id);
        $nama = $baris->nama_dokumen;
        $baris->delete();

        $this->dispatch('toast', tipe: 'sukses', pesan: "Dokumen {$nama} dihapus dari checklist default.");
    }

    public function render()
    {
        $daftarInstansi = Instansi::query()
            ->withCount('formasi')
            ->with(['checklistDefault' => fn ($q) => $q->orderBy('urutan')])
            ->orderBy('nama')
            ->get();

        return view('livewire.admin.instansi-manager', [
            'daftarInstansi' => $daftarInstansi,
        ]);
    }
}
