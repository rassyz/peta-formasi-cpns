<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Penempatan instansi besar (mis. Pemprov DKI Jakarta) memakai hierarki
     * panjang — hingga 353 karakter — dan slug gabungan jabatan + penempatan
     * hingga 402 karakter, melewati VARCHAR(255) awal. Lebarkan keduanya.
     * Indeks unik (musim_id, slug) tetap aman: 512 karakter utf8mb4 = 2.048
     * byte, di bawah batas kunci indeks InnoDB 3.072 byte.
     */
    public function up(): void
    {
        Schema::table('formasi', function (Blueprint $table) {
            $table->string('penempatan', 512)->change();
            $table->string('slug', 512)->change();
        });
    }

    public function down(): void
    {
        Schema::table('formasi', function (Blueprint $table) {
            $table->string('penempatan')->change();
            $table->string('slug')->change();
        });
    }
};
