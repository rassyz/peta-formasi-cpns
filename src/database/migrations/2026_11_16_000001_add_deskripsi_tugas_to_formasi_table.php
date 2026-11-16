<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom deskripsi tugas jabatan: teks asli pengumuman, opsional.
     * Hanya sebagian instansi menerbitkannya (mis. DKI Jakarta lewat kolom
     * DESKRIPSI PEKERJAAN, Kemendikbudristek Bab III, BKKBN Lampiran IV);
     * instansi lain kolom ini kosong dan bloknya tidak tampil di detail.
     */
    public function up(): void
    {
        Schema::table('formasi', function (Blueprint $table) {
            $table->text('deskripsi_tugas')->nullable()->after('syarat_khusus');
        });
    }

    public function down(): void
    {
        Schema::table('formasi', function (Blueprint $table) {
            $table->dropColumn('deskripsi_tugas');
        });
    }
};
