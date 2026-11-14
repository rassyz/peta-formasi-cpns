<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-A7: tamu boleh mengirim request — user_id nullable.
        Schema::create('request_formasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('musim_id')->nullable()->constrained('musim')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('instansi_dicari');
            $table->string('jabatan_dicari');
            $table->text('catatan')->nullable();
            $table->string('status', 20)->default('menunggu')->index(); // menunggu | diproses | selesai | ditolak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_formasi');
    }
};
