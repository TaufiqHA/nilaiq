<?php

namespace App\Models;

use Database\Factories\FinalScoresFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'final_exam_id',
    'student_id',
    'score',
    'created_at',
])]
class FinalScores extends Model
{
    /** @use HasFactory<FinalScoresFactory> */
    use HasFactory;

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'float',
        ];
    }

    /**
     * Get the final exam that owns the final score.
     */
    public function finalExam(): BelongsTo
    {
        return $this->belongsTo(FinalExams::class, 'final_exam_id');
    }

    /**
     * Get the student that owns the final score.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
