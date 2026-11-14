<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kuota per jenis kebutuhan sebagai BARIS (bukan kolom tetap) —
        // jenisnya berbeda antar musim & instansi (PRD §7.2).
        Schema::create('formasi_kuota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formasi_id')->constrained('formasi')->cascadeOnDelete();
            $table->string('jenis_kebutuhan', 30)->default('umum'); // umum | cumlaude | disabilitas | ...
            $table->unsignedInteger('jumlah')->default(0);
            $table->timestamps();

            $table->unique(['formasi_id', 'jenis_kebutuhan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formasi_kuota');
    }
};
