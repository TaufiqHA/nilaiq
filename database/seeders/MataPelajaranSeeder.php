<?php

namespace Database\Seeders;

use App\Models\subjects;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataPelajaran = [
            ['subject_name' => 'Pendidikan Agama'],
            ['subject_name' => 'Pendidikan Kewarganegaraan'],
            ['subject_name' => 'Bahasa Indonesia'],
            ['subject_name' => 'Bahasa Inggris'],
            ['subject_name' => 'Matematika'],
            ['subject_name' => 'Ilmu Pengetahuan Alam'],
            ['subject_name' => 'Ilmu Pengetahuan Sosial'],
            ['subject_name' => 'Seni Budaya'],
            ['subject_name' => 'Prakarya / Keterampilan'],
            ['subject_name' => 'Bahasa Daerah'],
        ];

        foreach ($mataPelajaran as $pelajaran) {
            subjects::create($pelajaran);
        }
    }
}
