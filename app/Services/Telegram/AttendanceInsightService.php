<?php

namespace App\Services\Telegram;

use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\Students;

/**
 * Computes attendance insights for a class using the same source data as the
 * website attendance recap.
 */
class AttendanceInsightService
{
    /**
     * @return array{
     *     class: Classes,
     *     meeting_count: int,
     *     rows: array<int, array{
     *         student: Students,
     *         hadir: int,
     *         izin: int,
     *         sakit: int,
     *         alfa: int,
     *         recorded: int,
     *         percentage: float|null
     *     }>,
     *     average_percentage: float|null
     * }
     */
    public function classAttendance(Classes $class): array
    {
        $meetings = AttendanceMeetings::where('class_id', $class->id)
            ->orderBy('meeting_date')
            ->orderBy('id')
            ->get();

        // Keep the newest row when the server has duplicate rows for the same
        // meeting/student pair, so a student is never counted twice.
        $attendances = Attendances::whereIn('attendance_meeting_id', $meetings->pluck('id'))
            ->orderByDesc('id')
            ->get()
            ->unique(fn (Attendances $attendance): string => $attendance->attendance_meeting_id.':'.$attendance->student_id)
            ->values();

        $byStudent = $attendances->groupBy('student_id');

        $students = Students::where('class_id', $class->id)
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        $rows = [];
        $percentages = [];

        foreach ($students as $student) {
            $records = $byStudent->get($student->id, collect());

            $hadir = $records->where('status', 'HADIR')->count();
            $izin = $records->where('status', 'IZIN')->count();
            $sakit = $records->where('status', 'SAKIT')->count();
            $alfa = $records->where('status', 'ALFA')->count();
            $recorded = $records->count();

            $percentage = $recorded > 0
                ? round(($hadir / $recorded) * 100, 1)
                : null;

            if ($percentage !== null) {
                $percentages[] = $percentage;
            }

            $rows[] = [
                'student' => $student,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alfa' => $alfa,
                'recorded' => $recorded,
                'percentage' => $percentage,
            ];
        }

        return [
            'class' => $class,
            'meeting_count' => $meetings->count(),
            'rows' => $rows,
            'average_percentage' => $percentages !== []
                ? round(array_sum($percentages) / count($percentages), 1)
                : null,
        ];
    }

    /**
     * Get a single student's attendance row from a class summary.
     *
     * @param  array{rows: array<int, array<string, mixed>>}  $summary
     * @return array<string, mixed>|null
     */
    public function studentRow(array $summary, int $studentId): ?array
    {
        foreach ($summary['rows'] as $row) {
            if ($row['student']->id === $studentId) {
                return $row;
            }
        }

        return null;
    }
}
