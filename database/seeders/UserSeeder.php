<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! User::where('email', 'administrator@nilaiq.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin NilaiQ',
                'email' => 'administrator@nilaiq.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        if (! User::where('email', 'wali@nilaiq.com')->exists()) {
            User::factory()->create([
                'name' => 'Wali Kelas',
                'email' => 'wali@nilaiq.com',
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
            ]);
        }

        if (! User::where('email', 'admin@nilaiq.com')->exists()) {
            User::factory()->create([
                'name' => 'Guru Mapel',
                'email' => 'admin@nilaiq.com',
                'password' => Hash::make('password'),
                'role' => 'mapel',
            ]);
        }
    }
}
