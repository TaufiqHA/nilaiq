<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\AcademicYear;
use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\CatatanWaliKelas;
use App\Models\Classes;
use App\Models\ClassWaliKelas;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\Ekskul;
use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\MapelSettings;
use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\NilaiMapel;
use App\Models\Prestasi;
use App\Models\Recaps;
use App\Models\Settings;
use App\Models\SettingsWaliKelas;
use App\Models\Sikap;
use App\Models\Students;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class OwnershipService
{
    /**
     * Determine whether the given user owns the record.
     *
     * Admins bypass ownership checks. Models that are not part of the
     * teacher data tenant (users, plans, orders) are ignored.
     */
    public function isOwnedBy(?Model $record, ?User $user): bool
    {
        if ($record === null || $user === null) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        return match (true) {
            $record instanceof AcademicYear,
            $record instanceof Settings,
            $record instanceof ClassWaliKelas => (int) $record->user_id === $user->id,

            $record instanceof Classes => $this->isOwnedBy($record->academicYear, $user),

            $record instanceof Students,
            $record instanceof AttendanceMeetings,
            $record instanceof DailyTestMeetings,
            $record instanceof AssignmentMeetings,
            $record instanceof MidtermExams,
            $record instanceof FinalExams => $this->isOwnedBy($record->class?->academicYear, $user),

            $record instanceof Attendances => $this->isOwnedBy(
                $record->attendanceMeeting?->class?->academicYear,
                $user,
            ),

            $record instanceof DailyTestScores => $this->isOwnedBy(
                $record->dailyTestMeeting?->class?->academicYear,
                $user,
            ),

            $record instanceof AssignmentScores => $this->isOwnedBy(
                $record->assignmentMeeting?->class?->academicYear,
                $user,
            ),

            $record instanceof MidtermScores => $this->isOwnedBy(
                $record->midtermExam?->class?->academicYear,
                $user,
            ),

            $record instanceof FinalScores => $this->isOwnedBy(
                $record->finalExam?->class?->academicYear,
                $user,
            ),

            $record instanceof Recaps => $this->isOwnedBy(
                $record->class?->academicYear,
                $user,
            ),

            $record instanceof StudentWaliKelas => $this->isOwnedBy(
                $record->classWaliKelas,
                $user,
            ),

            $record instanceof SettingsWaliKelas => $this->isOwnedBy(
                $record->academicYear,
                $user,
            ),

            $record instanceof MapelSettings => $this->isOwnedBy(
                $record->settingsWaliKelas?->academicYear,
                $user,
            ),

            $record instanceof NilaiMapel => $this->isOwnedBy(
                $record->student?->classWaliKelas,
                $user,
            ),

            $record instanceof Ekskul,
            $record instanceof Prestasi,
            $record instanceof Sikap,
            $record instanceof Absensi,
            $record instanceof CatatanWaliKelas => $this->isOwnedBy(
                $record->student?->classWaliKelas,
                $user,
            ),

            default => true,
        };
    }
}
