<?php

namespace App\Models;

use App\Observers\StudentObserver;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([StudentObserver::class])]
class students extends Model
{
    protected $guarded = ['id'];

    public function class()
    {
        return $this->belongsTo(classes::class, 'class_id', 'id');
    }

    public function subjectAttendanceRecords()
    {
        return $this->hasOne(subjectAttendanceRecords::class);
    }

    public function sikap()
    {
        return $this->hasOne(Attitudes::class, 'student_id');
    }
}
