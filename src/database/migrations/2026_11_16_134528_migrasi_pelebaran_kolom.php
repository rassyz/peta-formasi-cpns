<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
