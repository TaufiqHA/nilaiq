<?php

namespace App\Models;

use Database\Factories\StudentsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'class_id',
    'nis',
    'nisn',
    'name',
    'gender',
    'birth_place',
    'birth_date',
    'address',
    'parent_name',
    'parent_phone',
    'status',
])]
class Students extends Model
{
    /** @use HasFactory<StudentsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Get the class that owns the student.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the daily test scores for the student.
     */
    public function dailyTestScores(): HasMany
    {
        return $this->hasMany(DailyTestScores::class, 'student_id');
    }

    /**
     * Get the assignment scores for the student.
     */
    public function assignmentScores(): HasMany
    {
        return $this->hasMany(AssignmentScores::class, 'student_id');
    }

    /**
     * Get the midterm scores for the student.
     */
    public function midtermScores(): HasMany
    {
        return $this->hasMany(MidtermScores::class, 'student_id');
    }

    /**
     * Get the final scores for the student.
     */
    public function finalScores(): HasMany
    {
        return $this->hasMany(FinalScores::class, 'student_id');
    }
}
