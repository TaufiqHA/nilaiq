<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class students extends Model
{
    protected $guarded = ['id'];

    public function class()
    {
        return $this->belongsTo(classes::class, 'class_id', 'id');
    }

    public function subjectAttendanceRecords()
    {
        return $this->hasOne(subjectAttendanceRecords::class);
    }
}
