<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FR-E1/E2 + FR-AD4: 1 kode = 1 akun; status terlacak penuh.
        Schema::create('kode_akses', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 40)->unique();
            $table->string('tipe_pass', 20)->index(); // historis | strategi
            $table->foreignId('musim_id')->constrained('musim')->cascadeOnDelete();
            $table->string('status', 20)->default('belum_dipakai')->index(); // belum_dipakai | sudah_dipakai | dicabut
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // dipakai oleh
            $table->timestamp('dipakai_pada')->nullable();
            $table->string('batch')->nullable(); // catatan batch Admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kode_akses');
    }
};
