<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musim', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun')->unique();
            $table->string('label'); // mis. "CPNS 2024"
            $table->date('tanggal_acuan_usia'); // R-01: acuan hitung usia musim ini
            $table->date('tanggal_pengumuman')->nullable();
            $table->date('pendaftaran_mulai')->nullable();
            $table->date('pendaftaran_tutup')->nullable();
            // Status (PRD §7.1): testing | aktif | historis | arsip — string, bukan enum DB,
            // supaya siklus hidup bisa berkembang tanpa ALTER enum.
            $table->string('status', 20)->default('testing')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musim');
    }
};
