<?php

namespace App\Models;

use Database\Factories\MidtermExamsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'class_id',
    'title',
    'exam_date',
    'description',
])]
class MidtermExams extends Model
{
    /** @use HasFactory<MidtermExamsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
        ];
    }

    /**
     * Get the class that owns the midterm exam.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the scores for the midterm exam.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(MidtermScores::class, 'midterm_exam_id');
    }
}
