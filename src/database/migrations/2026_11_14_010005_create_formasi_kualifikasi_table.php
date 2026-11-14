<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pivot formasi <-> jurusan (R-03): satu formasi menerima banyak jurusan.
        Schema::create('formasi_kualifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formasi_id')->constrained('formasi')->cascadeOnDelete();
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['formasi_id', 'jurusan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formasi_kualifikasi');
    }
};
