<?php

namespace App\Models;

use Database\Factories\MidtermScoresFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_uuid',
    'midterm_exam_id',
    'student_id',
    'score',
    'created_at',
])]
class MidtermScores extends Model
{
    /** @use HasFactory<MidtermScoresFactory> */
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
     * Get the midterm exam that owns the midterm score.
     */
    public function midtermExam(): BelongsTo
    {
        return $this->belongsTo(MidtermExams::class, 'midterm_exam_id');
    }

    /**
     * Get the student that owns the midterm score.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    /**
     * Limit the query to scores owned by the given user.
     */
    public function scopeOwnedBy(Builder $query, User|int $user): Builder
    {
        return $query->whereHas(
            'midtermExam.class.academicYear',
            fn (Builder $relation): Builder => $relation->where('user_id', $user instanceof User ? $user->id : $user),
        );
    }
}
