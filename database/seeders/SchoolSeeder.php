<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('schools')->insert([
            [
                'school_name' => 'SMP Negeri 1 Tompobulu',
                'address' => 'Jl. Pendidikan No. 140, Malakaji, Tompobulu, Gowa',
                'academic_years_id' => null,
                'nss' => '201190306010',
                'npsn' => '40301067',
                'website' => 'http://www.smpn1tompobulu.sch.id',
                'email' => 'smptompobulu1@gmail.com',
                'phone' => '0411-123456',
                'principal_name' => 'Sulkifli, S. Pd., M. Pd.',
                'nip' => '19781219 201001 1 016',
            ],
            // Anda dapat menambahkan lebih banyak data sekolah di sini
        ]);
    }
}