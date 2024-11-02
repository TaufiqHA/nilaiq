<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\student;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportStudent implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $namaKelas = $row[1];
        $kelas = Kelas::where('name', $namaKelas)->first();
        return new student([
            'name' => $row[0],
            'kelas_id' => $kelas ? $kelas->id : null,
        ]);
    }
}
