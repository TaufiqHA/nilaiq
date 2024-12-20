<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subjectAttendanceSessions extends Model
{
    protected $guarded = ['id'];

    public function class()
    {
        return $this->belongsTo(classes::class);
    }

    public function subject()
    {
        return $this->belongsTo(subjects::class);
    }

    public function teacher()
    {
        return $this->belongsTo(teachers::class);
    }
}
