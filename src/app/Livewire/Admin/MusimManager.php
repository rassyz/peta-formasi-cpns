<?php

namespace App\Livewire\Admin;

use App\Models\Formasi;
use App\Models\KodeAkses;
use App\Models\Musim;
use App\Models\UserPass;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Admin: kelola musim seleksi — buat/edit musim beserta tanggal-tanggalnya,
 * dan ganti status mengikuti siklus hidup PRD §7 (dibuat → aktif → historis
 * → arsip, hanya maju). Dua penjaga penting:
 *  - maksimal SATU musim berstatus aktif (mengaktifkan musim ditolak selama
 *    masih ada musim aktif lain);
 *  - hapus hanya untuk musim "dibuat" yang belum punya data apa pun.
 * Tanggal musim berdampak lintas fitur: acuan usia menentukan cek kelayakan,
 * dan pendaftaran_tutup menentukan masa berlaku Pass Strategi.
 */
#[Layout('components.layouts.admin')]
#[Title('Kelola Musim — Admin Peta Formasi')]
class MusimManager extends Component
{
    public string $pesan = '';

    public string $galat = '';

    // --- Form buat/edit ---
    public bool $tampilForm = false;

    public ?int $editId = null;

    public string $tahun = '';

    public string $label = '';

    public string $acuanUsia = '';

    public string $tanggalPengumuman = '';

    public string $pendaftaranMulai = '';

    public string $pendaftaranTutup = '';

    /** Urutan siklus hidup musim (PRD §7). */
    private const SIKLUS = ['dibuat', 'aktif', 'historis', 'arsip'];

    public function bukaBuat(): void
    {
        $this->reset(['editId', 'tahun', 'label', 'acuanUsia', 'tanggalPengumuman', 'pendaftaranMulai', 'pendaftaranTutup']);
        $this->pesan = '';
        $this->galat = '';
        $this->tampilForm = true;
    }

    public function bukaEdit(int $id): void
    {
        $musim = Musim::query()->findOrFail($id);
        $this->editId = $musim->id;
        $this->tahun = (string) $musim->tahun;
        $this->label = $musim->label;
        $this->acuanUsia = $musim->tanggal_acuan_usia?->format('Y-m-d') ?? '';
        $this->tanggalPengumuman = $musim->tanggal_pengumuman?->format('Y-m-d') ?? '';
        $this->pendaftaranMulai = $musim->pendaftaran_mulai?->format('Y-m-d') ?? '';
        $this->pendaftaranTutup = $musim->pendaftaran_tutup?->format('Y-m-d') ?? '';
        $this->pesan = '';
        $this->galat = '';
        $this->tampilForm = true;
    }

    public function batal(): void
    {
        $this->tampilForm = false;
        $this->reset(['editId', 'tahun', 'label', 'acuanUsia', 'tanggalPengumuman', 'pendaftaranMulai', 'pendaftaranTutup']);
    }

    public function simpan(): void
    {
        $this->pesan = '';
        $this->galat = '';

        $data = $this->validate([
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100', Rule::unique('musim', 'tahun')->ignore($this->editId)],
            'label' => ['required', 'string', 'max:60'],
            'acuanUsia' => ['required', 'date'],
            'tanggalPengumuman' => ['nullable', 'date'],
            'pendaftaranMulai' => ['nullable', 'date'],
            'pendaftaranTutup' => array_merge(
                ['nullable', 'date'],
                $this->pendaftaranMulai !== '' ? ['after_or_equal:pendaftaranMulai'] : []
            ),
        ]);

        $isian = [
            'tahun' => (int) $data['tahun'],
            'label' => $data['label'],
            'tanggal_acuan_usia' => $data['acuanUsia'],
            'tanggal_pengumuman' => $data['tanggalPengumuman'] !== '' ? $data['tanggalPengumuman'] : null,
            'pendaftaran_mulai' => $data['pendaftaranMulai'] !== '' ? $data['pendaftaranMulai'] : null,
            'pendaftaran_tutup' => $data['pendaftaranTutup'] !== '' ? $data['pendaftaranTutup'] : null,
        ];

        if ($this->editId !== null) {
            Musim::query()->whereKey($this->editId)->update($isian);
            $this->pesan = 'Musim "' . $isian['label'] . '" diperbarui.';
        } else {
            Musim::create($isian + ['status' => 'dibuat']);
            $this->pesan = 'Musim "' . $isian['label'] . '" dibuat dengan status "Dibuat" — isi dulu datanya, aktifkan bila sudah siap tampil.';
        }

        $this->batal();
    }

    public function transisi(int $id, string $ke): void
    {
        $this->pesan = '';
        $this->galat = '';

        $musim = Musim::query()->findOrFail($id);

        $idxLama = array_search($musim->status, self::SIKLUS, true);
        $idxBaru = array_search($ke, self::SIKLUS, true);

        if ($idxLama === false || $idxBaru === false || $idxBaru !== $idxLama + 1) {
            $this->galat = 'Transisi dari "' . $musim->status . '" ke "' . $ke . '" tidak mengikuti siklus musim (dibuat → aktif → historis → arsip).';
            return;
        }

        if ($ke === 'aktif') {
            $aktifLain = Musim::query()->aktif()->whereKeyNot($musim->id)->first();
            if ($aktifLain !== null) {
                $this->galat = 'Musim "' . $aktifLain->label . '" masih berstatus Aktif. Ubah dulu musim itu menjadi Historis — hanya boleh ada satu musim aktif pada satu waktu.';
                return;
            }
        }

        $musim->update(['status' => $ke]);
        $this->pesan = 'Status "' . $musim->label . '" kini ' . ucfirst($ke) . '.';
    }

    public function hapus(int $id): void
    {
        $this->pesan = '';
        $this->galat = '';

        $musim = Musim::query()->findOrFail($id);

        if ($musim->status !== 'dibuat') {
            $this->galat = 'Hanya musim berstatus "Dibuat" yang bisa dihapus. Musim yang sudah pernah aktif diarsipkan saja, tidak dihapus.';
            return;
        }

        $terpakai = Formasi::query()->where('musim_id', $id)->exists()
            || KodeAkses::query()->where('musim_id', $id)->exists()
            || UserPass::query()->where('musim_id', $id)->exists();

        if ($terpakai) {
            $this->galat = 'Musim "' . $musim->label . '" sudah memiliki formasi/kode/pass sehingga tidak bisa dihapus.';
            return;
        }

        $musim->delete();
        $this->pesan = 'Musim "' . $musim->label . '" dihapus.';
    }

    public function render()
    {
        return view('livewire.admin.musim-manager', [
            'daftarMusim' => Musim::query()
                ->withCount('formasi')
                ->orderByDesc('tahun')
                ->get(),
            'siklus' => self::SIKLUS,
        ]);
    }
}
