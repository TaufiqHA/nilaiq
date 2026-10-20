<?php

namespace App\Models;

use Database\Factories\FinalExamsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'client_uuid',
    'class_id',
    'title',
    'exam_date',
    'description',
])]
class FinalExams extends Model
{
    /** @use HasFactory<FinalExamsFactory> */
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
     * Get the class that owns the final exam.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the scores for the final exam.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(FinalScores::class, 'final_exam_id');
    }

    /**
     * Limit the query to exams owned by the given user.
     */
    public function scopeOwnedBy(Builder $query, User|int $user): Builder
    {
        return $query->whereHas(
            'class.academicYear',
            fn (Builder $relation): Builder => $relation->where('user_id', $user instanceof User ? $user->id : $user),
        );
    }
}
