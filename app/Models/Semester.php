<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    protected $guarded = ['id'];

    public function school()
    {
        return $this->hasOne(schools::class);
    }
}
