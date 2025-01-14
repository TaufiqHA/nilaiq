<?php

namespace App\Observers;

use App\Models\Attitudes;
use App\Models\students;

class StudentObserver
{
    /**
     * Handle the students "created" event.
     */
    public function created(students $students): void
    {
        Attitudes::create([
            'student_id' => $students->id,
            'faith_and_piety' => null,
            'independent' => null,
            'teamwork' => null,
            'creative' => null,
            'critical_thinking' => null,
            'global_diversity' => null,
        ]);
    }

    /**
     * Handle the students "updated" event.
     */
    public function updated(students $students): void
    {
        //
    }

    /**
     * Handle the students "deleted" event.
     */
    public function deleted(students $students): void
    {
        //
    }

    /**
     * Handle the students "restored" event.
     */
    public function restored(students $students): void
    {
        //
    }

    /**
     * Handle the students "force deleted" event.
     */
    public function forceDeleted(students $students): void
    {
        //
    }
}
