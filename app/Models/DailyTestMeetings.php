<?php

namespace App\Models;

use Database\Factories\DailyTestMeetingsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'class_id',
    'title',
    'test_date',
    'description',
])]
class DailyTestMeetings extends Model
{
    /** @use HasFactory<DailyTestMeetingsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'test_date' => 'date',
        ];
    }

    /**
     * Get the class that owns the daily test meeting.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the scores for the daily test meeting.
     */
    public function scores(): HasMany
    {
        return $this->hasMany(DailyTestScores::class, 'daily_test_meeting_id');
    }
}
