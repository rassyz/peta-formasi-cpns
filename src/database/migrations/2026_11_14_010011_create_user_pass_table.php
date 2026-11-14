<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-E3: Pass Historis aktif_hingga null (tanpa batas tanggal selama musim terbit);
        // Pass Strategi berakhir 30 hari setelah pendaftaran musimnya ditutup.
        Schema::create('user_pass', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kode_akses_id')->unique()->constrained('kode_akses')->cascadeOnDelete(); // 1 kode = tepat 1 pass
            $table->string('tipe_pass', 20)->index(); // historis | strategi
            $table->foreignId('musim_id')->constrained('musim')->cascadeOnDelete();
            $table->timestamp('aktif_dari');
            $table->timestamp('aktif_hingga')->nullable();
            $table->string('status', 20)->default('aktif')->index(); // aktif | berakhir
            $table->timestamps();

            $table->index(['user_id', 'musim_id', 'tipe_pass']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_pass');
    }
};
