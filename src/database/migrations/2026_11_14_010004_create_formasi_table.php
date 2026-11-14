<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = jabatan x penempatan dalam satu musim (PRD §7.2).
        // Kuota TIDAK disimpan di sini — adanya di formasi_kuota per jenis kebutuhan.
        Schema::create('formasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musim_id')->constrained('musim')->cascadeOnDelete();
            $table->foreignId('instansi_id')->constrained('instansi')->cascadeOnDelete();
            $table->string('jabatan');
            $table->string('penempatan');
            $table->string('slug');
            // Syarat kunci terstruktur (Modul B / Bagian 6 PRD)
            $table->unsignedTinyInteger('usia_min')->nullable();
            $table->unsignedTinyInteger('usia_maks')->nullable();
            $table->decimal('ipk_min', 3, 2)->nullable(); // nullable: tidak semua formasi menetapkan
            $table->boolean('skl_diterima')->default(false); // R-05
            $table->string('syarat_akreditasi', 20)->nullable(); // null = tidak disyaratkan; 'wajib' = R-06
            $table->text('syarat_khusus')->nullable(); // teks asli pengumuman (R-08, cek mandiri)
            // Gaji: hanya diisi bila ada sumber rujukan (PRD §8)
            $table->unsignedBigInteger('gaji_min')->nullable();
            $table->unsignedBigInteger('gaji_maks')->nullable();
            $table->string('sumber_gaji')->nullable();
            $table->date('tanggal_verifikasi_gaji')->nullable();
            // Provenance data formasi
            $table->string('sumber_pengumuman')->nullable();
            $table->date('tanggal_verifikasi')->nullable();
            // Status terbit: draft | terbit (gerbang verifikasi FR-AD2/AD5)
            $table->string('status_terbit', 20)->default('draft')->index();
            $table->boolean('kartu_contoh')->default(false); // FR-A8: maks 3 per jurusan, ditegakkan admin
            $table->timestamps();

            $table->unique(['musim_id', 'slug']);
            $table->index(['musim_id', 'status_terbit']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formasi');
    }
};
