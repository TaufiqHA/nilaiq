<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class guruMataPelajaran extends Model
{
    protected $guarded = ['id'];

    public function teacher()
    {
        return $this->belongsTo(teachers::class);
    }

    public function subject()
    {
        return $this->belongsTo(subjects::class);
    }
}
