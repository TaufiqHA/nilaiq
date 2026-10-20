<?php

namespace App\Models;

use Database\Factories\DailyTestScoresFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_uuid',
    'daily_test_meeting_id',
    'student_id',
    'score',
    'created_at',
])]
class DailyTestScores extends Model
{
    /** @use HasFactory<DailyTestScoresFactory> */
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
     * Get the daily test meeting that owns the daily test score.
     */
    public function dailyTestMeeting(): BelongsTo
    {
        return $this->belongsTo(DailyTestMeetings::class, 'daily_test_meeting_id');
    }

    /**
     * Get the student that owns the daily test score.
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
            'dailyTestMeeting.class.academicYear',
            fn (Builder $relation): Builder => $relation->where('user_id', $user instanceof User ? $user->id : $user),
        );
    }
}
