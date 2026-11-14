<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel master jurusan (PRD §7.2): satu-satunya sumber ejaan nama jurusan yang sah.
        Schema::create('jurusan', function (Blueprint $table) {
            $table->id();
            $table->string('jenjang', 10); // D3 | D4 | S1 | S2 | Profesi
            $table->string('nama');
            $table->string('slug')->unique(); // memuat jenjang, mis. s1-teknik-informatika
            $table->timestamps();

            $table->unique(['jenjang', 'nama']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurusan');
    }
};
