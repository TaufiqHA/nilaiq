<?php

namespace App\Models;

use Database\Factories\AttendancesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'attendance_meeting_id',
    'student_id',
    'status',
    'note',
])]
class Attendances extends Model
{
    /** @use HasFactory<AttendancesFactory> */
    use HasFactory;

    /**
     * Get the attendance meeting that owns the attendance.
     */
    public function attendanceMeeting(): BelongsTo
    {
        return $this->belongsTo(AttendanceMeetings::class, 'attendance_meeting_id');
    }

    /**
     * Get the student that owns the attendance.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Students::class, 'student_id');
    }
}
