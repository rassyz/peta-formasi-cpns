<?php

namespace App\Livewire\Admin\Concerns;

/**
 * Konfirmasi aksi admin di dalam aplikasi — pengganti wire:confirm bawaan
 * browser (dialog "situs.test says" yang tidak bisa diatur gayanya), sejak
 * 15 Nov 2026.
 *
 * Tombol aksi memanggil mintaKonfirmasi() dengan nama method + argumen
 * aslinya; partial bersama livewire/admin/_modal-konfirmasi menampilkannya
 * sebagai modal bergaya sistem; tombol setuju memanggil
 * jalankanKonfirmasi() yang meneruskan panggilan ke method aslinya, dan
 * hasil aksi itu sendiri dilaporkan lewat sistem toast.
 */
trait KonfirmasiAksi
{
    /**
     * @var array{aksi: string, argumen: array<int, mixed>, judul: string, pesan: string, bahaya: bool, label: string}|null
     */
    public ?array $dialogKonfirmasi = null;

    /**
     * @param  array<int, mixed>  $argumen
     */
    public function mintaKonfirmasi(string $aksi, array $argumen, string $judul, string $pesan, bool $bahaya = false, string $label = 'Ya, lanjutkan'): void
    {
        $this->dialogKonfirmasi = [
            'aksi' => $aksi,
            'argumen' => $argumen,
            'judul' => $judul,
            'pesan' => $pesan,
            'bahaya' => $bahaya,
            'label' => $label,
        ];
    }

    public function batalKonfirmasi(): void
    {
        $this->dialogKonfirmasi = null;
    }

    public function jalankanKonfirmasi(): void
    {
        $dialog = $this->dialogKonfirmasi;
        $this->dialogKonfirmasi = null;

        if ($dialog !== null) {
            $this->{$dialog['aksi']}(...$dialog['argumen']);
        }
    }
}
