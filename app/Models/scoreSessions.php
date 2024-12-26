<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class scoreSessions extends Model
{
    protected $guarded = ['id'];

    public function subject()
    {
        return $this->belongsTo(subjects::class);
    }

    public function class()
    {
        return $this->belongsTo(classes::class);
    }
}
