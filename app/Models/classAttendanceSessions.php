<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class classAttendanceSessions extends Model
{
    protected $guarded = ['id'];

    public function class()
    {
        return $this->belongsTo(classes::class);
    }
}
