<?php

namespace App\Models;

use Database\Factories\AssignmentScoresFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_uuid',
    'assignment_meeting_id',
    'student_id',
    'score',
    'created_at',
])]
class AssignmentScores extends Model
{
    /** @use HasFactory<AssignmentScoresFactory> */
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
     * Get the assignment meeting that owns the assignment score.
     */
    public function assignmentMeeting(): BelongsTo
    {
        return $this->belongsTo(AssignmentMeetings::class, 'assignment_meeting_id');
    }

    /**
     * Get the student that owns the assignment score.
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
            'assignmentMeeting.class.academicYear',
            fn (Builder $relation): Builder => $relation->where('user_id', $user instanceof User ? $user->id : $user),
        );
    }
}
