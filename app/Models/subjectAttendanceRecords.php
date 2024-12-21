<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subjectAttendanceRecords extends Model
{
    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(students::class);
    }
}
