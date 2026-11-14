<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Warisan checklist (PRD §7.2): baris default menempel di instansi
        // (formasi_id null); formasi hanya menyimpan baris override bila berbeda.
        Schema::create('checklist_berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instansi_id')->constrained('instansi')->cascadeOnDelete();
            $table->foreignId('formasi_id')->nullable()->constrained('formasi')->cascadeOnDelete();
            $table->string('nama_dokumen');
            $table->string('format', 50)->nullable(); // mis. "PDF", "JPG"
            $table->unsignedInteger('ukuran_maks_kb')->nullable();
            $table->boolean('wajib')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['instansi_id', 'formasi_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_berkas');
    }
};
