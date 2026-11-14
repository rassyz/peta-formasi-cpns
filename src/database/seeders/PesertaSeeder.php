<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun peserta contoh untuk pengujian manual (DEV SAJA — jangan jalan di produksi).
 * Semua peserta polos: role peserta, TANPA pass/kode akses apa pun.
 * Kata sandi semuanya: "password" (konvensi yang sama dengan AdminUserSeeder).
 *
 * Idempoten: aman dijalankan berulang (updateOrCreate berdasarkan email).
 */
class PesertaSeeder extends Seeder
{
    public function run(): void
    {
        $peserta = [
            ['name' => 'Budi Santoso', 'email' => 'budi@formasi.test'],
            ['name' => 'Sari Wulandari', 'email' => 'sari@formasi.test'],
            ['name' => 'Andi Pratama', 'email' => 'andi@formasi.test'],
        ];

        foreach ($peserta as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'role' => 'peserta',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
