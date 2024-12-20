<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class waliKelas extends Model
{
    protected $guarded = ['id'];

    public function teacher()
    {
        return $this->belongsTo(teachers::class);
    }

    public function class()
    {
        return $this->belongsTo(classes::class);
    }
}
