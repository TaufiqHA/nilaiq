<?php

namespace App\Models;

use App\Models\Semester;
use Illuminate\Database\Eloquent\Model;

class schools extends Model
{
    protected $guarded = ['id'];

    public function classess()
    {
        return $this->hasMany(classes::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(academicYear::class, 'academic_years_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
