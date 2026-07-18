<?php

namespace App\Models;

use Database\Factories\ClassesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_year_id',
    'name',
])]
class Classes extends Model
{
    /** @use HasFactory<ClassesFactory> */
    use HasFactory;

    /**
     * Get the academic year that owns the class.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the students for the class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Students::class, 'class_id');
    }

    /**
     * Get the attendance meetings for the class.
     */
    public function attendanceMeetings(): HasMany
    {
        return $this->hasMany(AttendanceMeetings::class, 'class_id');
    }
}
