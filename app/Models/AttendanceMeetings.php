<?php

namespace App\Models;

use Database\Factories\AttendanceMeetingsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'class_id',
    'title',
    'meeting_date',
    'description',
    'tipe',
])]
class AttendanceMeetings extends Model
{
    /** @use HasFactory<AttendanceMeetingsFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
        ];
    }

    /**
     * Get the class that owns the meeting.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the attendances for the meeting.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendances::class, 'attendance_meeting_id');
    }
}
