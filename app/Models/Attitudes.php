<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attitudes extends Model
{
    protected $guarded = ['id'];

    public function siswa()
    {
        return $this->belongsTo(students::class, 'student_id');
    }

}
