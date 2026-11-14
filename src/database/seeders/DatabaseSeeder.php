<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\Bpk2024Seeder;
use Database\Seeders\PesertaSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@formasi.test',
        ]);

        $this->call([
            AdminUserSeeder::class,
            Bpk2024Seeder::class,
            PesertaSeeder::class,
        ]);
    }
}
