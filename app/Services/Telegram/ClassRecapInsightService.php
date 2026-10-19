<?php

namespace App\Services\Telegram;

use App\Http\Controllers\Concerns\CalculatesClassRecap;
use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\Classes;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\Recaps;
use App\Models\Students;
use Illuminate\Support\Collection;

/**
 * Builds score recaps for the Telegram bot using the same calculation as the
 * website's Nilai Akhir / Rekap Nilai pages.
 */
class ClassRecapInsightService
{
    use CalculatesClassRecap;

    /**
     * @return array{
     *     class: Classes,
     *     total_meetings: int,
     *     assessment_counts: array<string, int>,
     *     rows: array<int, array{
     *         student: Students,
     *         rata_rata: float,
     *         jumlah: float,
     *         peringkat: int,
     *         final_score: int|null,
     *         score_count: int,
     *         type_scores: array<string, array{average: float|null, count: int}>
     *     }>
     * }
     */
    public function classRecap(Classes $class): array
    {
        $recapData = $this->buildClassRecap($class);

        $dailyMeetingIds = DailyTestMeetings::where('class_id', $class->id)->pluck('id');
        $assignmentMeetingIds = AssignmentMeetings::where('class_id', $class->id)->pluck('id');
        $midtermExamIds = MidtermExams::where('class_id', $class->id)->pluck('id');
        $finalExamIds = FinalExams::where('class_id', $class->id)->pluck('id');

        $dailyScores = DailyTestScores::whereIn('daily_test_meeting_id', $dailyMeetingIds)
            ->get()
            ->groupBy('student_id');
        $assignmentScores = AssignmentScores::whereIn('assignment_meeting_id', $assignmentMeetingIds)
            ->get()
            ->groupBy('student_id');
        $midtermScores = MidtermScores::whereIn('midterm_exam_id', $midtermExamIds)
            ->get()
            ->groupBy('student_id');
        $finalScores = FinalScores::whereIn('final_exam_id', $finalExamIds)
            ->get()
            ->groupBy('student_id');

        $students = Students::where('class_id', $class->id)
            ->where('status', 'ACTIVE')
            ->orderBy('name')
            ->get();

        $recapsKeyed = Recaps::where('class_id', $class->id)
            ->get()
            ->keyBy('student_id');

        $finalScoreMap = $this->resolveFinalScores($recapData, $students, $recapsKeyed);

        $rows = [];

        foreach ($recapData['rekap'] as $item) {
            $student = $item['student'];

            $daily = $dailyScores->get($student->id, collect());
            $assignments = $assignmentScores->get($student->id, collect());
            $midterms = $midtermScores->get($student->id, collect());
            $finals = $finalScores->get($student->id, collect());

            $typeScores = [
                TelegramIntent::ASSESSMENT_DAILY_TEST => $this->average($daily),
                TelegramIntent::ASSESSMENT_ASSIGNMENT => $this->average($assignments),
                TelegramIntent::ASSESSMENT_MIDTERM => $this->average($midterms),
                TelegramIntent::ASSESSMENT_FINAL => $this->average($finals),
            ];

            $rows[] = [
                'student' => $student,
                'rata_rata' => (float) $item['rata_rata'],
                'jumlah' => (float) $item['jumlah'],
                'peringkat' => (int) $item['peringkat'],
                'final_score' => $finalScoreMap->get($student->id),
                'score_count' => array_sum(array_column($typeScores, 'count')),
                'type_scores' => $typeScores,
            ];
        }

        return [
            'class' => $class,
            'total_meetings' => $recapData['total_meetings'],
            'assessment_counts' => [
                TelegramIntent::ASSESSMENT_DAILY_TEST => $dailyMeetingIds->count(),
                TelegramIntent::ASSESSMENT_ASSIGNMENT => $assignmentMeetingIds->count(),
                TelegramIntent::ASSESSMENT_MIDTERM => $midtermExamIds->count(),
                TelegramIntent::ASSESSMENT_FINAL => $finalExamIds->count(),
            ],
            'rows' => $rows,
        ];
    }

    /**
     * Rank students by the average of one assessment type.
     *
     * @return array<int, array{student: Students, average: float, count: int}>
     */
    public function assessmentRanking(Classes $class, string $type): array
    {
        $recap = $this->classRecap($class);

        $rows = [];

        foreach ($recap['rows'] as $row) {
            $typeScore = $row['type_scores'][$type] ?? null;

            if ($typeScore === null || $typeScore['count'] === 0 || $typeScore['average'] === null) {
                continue;
            }

            $rows[] = [
                'student' => $row['student'],
                'average' => $typeScore['average'],
                'count' => $typeScore['count'],
            ];
        }

        usort($rows, fn (array $a, array $b): int => $b['average'] <=> $a['average']);

        return $rows;
    }

    /**
     * @param  array{rows: array<int, array<string, mixed>>}  $recap
     * @return array<string, mixed>|null
     */
    public function studentRow(array $recap, int $studentId): ?array
    {
        foreach ($recap['rows'] as $row) {
            if ($row['student']->id === $studentId) {
                return $row;
            }
        }

        return null;
    }

    /**
     * @param  Collection<int, mixed>  $scores
     * @return array{average: float|null, count: int}
     */
    private function average($scores): array
    {
        if ($scores->isEmpty()) {
            return ['average' => null, 'count' => 0];
        }

        return [
            'average' => round((float) $scores->avg('score'), 2),
            'count' => $scores->count(),
        ];
    }
}
