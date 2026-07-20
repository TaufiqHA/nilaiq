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
        User::factory()->create([
            'name' => 'Admin NilaiQ',
            'email' => 'admin@nilaiq.com',
            'password' => Hash::make('password'),
            'role' => 'mapel',
        ]);

        User::factory()->create([
            'name' => 'Wali Kelas',
            'email' => 'wali@nilaiq.com',
            'password' => Hash::make('password'),
            'role' => 'wali_kelas',
        ]);
    }
}
