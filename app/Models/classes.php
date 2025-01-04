<?php

namespace App\Models;

use App\Models\schools;
use Illuminate\Database\Eloquent\Model;

class classes extends Model
{
    protected $guarded = ['id'];

    public function school()
    {
        return $this->belongsTo(schools::class);
    }

    public function students()
    {
        return $this->hasMany(students::class, 'class_id', 'id');
    }
}
