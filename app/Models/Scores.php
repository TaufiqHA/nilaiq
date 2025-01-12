<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scores extends Model
{
    protected $guarded = ['id'];

    public function student()
    {
        return $this->belongsTo(students::class);
    }

    public function class()
    {
        return $this->belongsTo(classes::class);
    }
}
