<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Akun admin awal untuk pengembangan (Tahap 0).
     *
     * Kredensial default: admin@peta-formasi.test / password
     * Override lewat env ADMIN_EMAIL & ADMIN_PASSWORD bila tersedia.
     * WAJIB diganti sebelum rilis publik (Tahap 7).
     */
    public function run(): void
    {
        $admin = User::firstOrNew([
            'email' => env('ADMIN_EMAIL', 'admin@petaformasi.test'),
        ]);

        $admin->forceFill([
            'name' => 'Admin Peta Formasi',
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            'role' => 'admin',
            'email_verified_at' => now(),
        ])->save();
    }
}
