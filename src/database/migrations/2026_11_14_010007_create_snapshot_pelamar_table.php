<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Snapshot selalu baris baru bertanggal; tidak pernah ditimpa (FR-C1).
        Schema::create('snapshot_pelamar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formasi_kuota_id')->constrained('formasi_kuota')->cascadeOnDelete();
            $table->string('tipe', 10)->index(); // historis | live
            $table->unsignedInteger('jumlah_ms'); // pelamar Memenuhi Syarat menurut sumber
            $table->unsignedInteger('jumlah_total')->nullable(); // hanya bila sumber menyediakan
            $table->date('tanggal_snapshot');
            $table->string('sumber')->nullable();
            $table->timestamps();

            $table->index(['formasi_kuota_id', 'tanggal_snapshot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snapshot_pelamar');
    }
};
