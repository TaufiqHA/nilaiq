<?php

namespace App\Http\Controllers\Concerns;

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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

trait CalculatesClassRecap
{
    /**
     * Build the score recap for every active student in a class.
     *
     * @return array{
     *     meetings: array{daily_tests: Collection, assignments: Collection, midterms: Collection, finals: Collection},
     *     rekap: array<int, array{student: Students, scores: array<string, array<int, float>>, jumlah: float, rata_rata: float}>,
     *     total_meetings: int,
     *     latest_assessment_at: Carbon|null
     * }
     */
    protected function buildClassRecap(Classes $class): array
    {
        $classId = $class->id;
        $students = $class->students()->where('status', 'ACTIVE')->get();

        // Get all meetings/exams for the class in chronological order.
        $dailyTestMeetings = DailyTestMeetings::where('class_id', $classId)->orderBy('id')->get();
        $assignmentMeetings = AssignmentMeetings::where('class_id', $classId)->orderBy('id')->get();
        $midtermExams = MidtermExams::where('class_id', $classId)->orderBy('id')->get();
        $finalExams = FinalExams::where('class_id', $classId)->orderBy('id')->get();

        // Fetch scores.
        $dailyScores = DailyTestScores::whereIn('daily_test_meeting_id', $dailyTestMeetings->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $assignmentScores = AssignmentScores::whereIn('assignment_meeting_id', $assignmentMeetings->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $midtermScores = MidtermScores::whereIn('midterm_exam_id', $midtermExams->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $finalScores = FinalScores::whereIn('final_exam_id', $finalExams->pluck('id'))
            ->get()
            ->groupBy('student_id');

        $totalMeetingsCount = $dailyTestMeetings->count() + $assignmentMeetings->count() + $midtermExams->count() + $finalExams->count();

        $latestAssessmentAt = $this->latestAssessmentTimestamp(
            $dailyTestMeetings,
            $assignmentMeetings,
            $midtermExams,
            $finalExams,
            $dailyScores,
            $assignmentScores,
            $midtermScores,
            $finalScores,
        );

        $rekapData = [];
        foreach ($students as $student) {
            $studentScores = [
                'daily_tests' => [],
                'assignments' => [],
                'midterms' => [],
                'finals' => [],
            ];

            $totalScore = 0;

            // Map Daily Test scores.
            $studentDaily = $dailyScores->get($student->id) ?? collect();
            foreach ($dailyTestMeetings as $meeting) {
                $scoreObj = $studentDaily->firstWhere('daily_test_meeting_id', $meeting->id);
                $scoreVal = $scoreObj ? floatval($scoreObj->score) : 0;
                $studentScores['daily_tests'][$meeting->id] = $scoreVal;
                $totalScore += $scoreVal;
            }

            // Map Assignment scores.
            $studentAssignment = $assignmentScores->get($student->id) ?? collect();
            foreach ($assignmentMeetings as $meeting) {
                $scoreObj = $studentAssignment->firstWhere('assignment_meeting_id', $meeting->id);
                $scoreVal = $scoreObj ? floatval($scoreObj->score) : 0;
                $studentScores['assignments'][$meeting->id] = $scoreVal;
                $totalScore += $scoreVal;
            }

            // Map Midterm scores.
            $studentMidterm = $midtermScores->get($student->id) ?? collect();
            foreach ($midtermExams as $exam) {
                $scoreObj = $studentMidterm->firstWhere('midterm_exam_id', $exam->id);
                $scoreVal = $scoreObj ? floatval($scoreObj->score) : 0;
                $studentScores['midterms'][$exam->id] = $scoreVal;
                $totalScore += $scoreVal;
            }

            // Map Final scores.
            $studentFinal = $finalScores->get($student->id) ?? collect();
            foreach ($finalExams as $exam) {
                $scoreObj = $studentFinal->firstWhere('final_exam_id', $exam->id);
                $scoreVal = $scoreObj ? floatval($scoreObj->score) : 0;
                $studentScores['finals'][$exam->id] = $scoreVal;
                $totalScore += $scoreVal;
            }

            $rataRata = $totalMeetingsCount > 0 ? ($totalScore / $totalMeetingsCount) : 0;

            $rekapData[] = [
                'student' => $student,
                'scores' => $studentScores,
                'jumlah' => round($totalScore, 2),
                'rata_rata' => round($rataRata, 2),
            ];
        }

        // Sort by rata_rata descending, and alphabetically by name as secondary sort for ties.
        usort($rekapData, function (array $a, array $b): int {
            if ($b['rata_rata'] === $a['rata_rata']) {
                return $a['student']->name <=> $b['student']->name;
            }

            return $b['rata_rata'] <=> $a['rata_rata'];
        });

        // Assign rankings.
        $rank = 1;
        $prevScore = null;
        foreach ($rekapData as $index => &$data) {
            if ($prevScore !== null && $data['rata_rata'] < $prevScore) {
                $rank = $index + 1;
            }
            $data['peringkat'] = $rank;
            $prevScore = $data['rata_rata'];
        }
        unset($data);

        return [
            'meetings' => [
                'daily_tests' => $dailyTestMeetings,
                'assignments' => $assignmentMeetings,
                'midterms' => $midtermExams,
                'finals' => $finalExams,
            ],
            'rekap' => $rekapData,
            'total_meetings' => $totalMeetingsCount,
            'latest_assessment_at' => $latestAssessmentAt,
        ];
    }

    /**
     * Resolve the final score for each student.
     *
     * A saved final score is used until a newer assessment (meeting or score)
     * exists, after which the automatically calculated average takes over.
     *
     * @param  array{rekap: array<int, array{student: Students, rata_rata: float}>, total_meetings: int, latest_assessment_at: Carbon|null}  $recapData
     * @param  Collection<int, Students>  $students
     * @param  Collection<int, Recaps>  $recapsKeyed
     * @return Collection<int, float|null>
     */
    protected function resolveFinalScores(array $recapData, Collection $students, Collection $recapsKeyed): Collection
    {
        $autoScores = collect($recapData['rekap'])
            ->mapWithKeys(fn (array $item): array => [$item['student']->id => (float) $item['rata_rata']]);

        $hasAssessments = $recapData['total_meetings'] > 0;
        $latestAssessmentAt = $recapData['latest_assessment_at'];

        return $students->mapWithKeys(function ($student) use ($autoScores, $hasAssessments, $latestAssessmentAt, $recapsKeyed): array {
            $recap = $recapsKeyed->get($student->id);
            $autoScore = $hasAssessments ? $autoScores->get($student->id) : null;
            $savedScore = $recap?->final_score_result;

            $savedScoreIsCurrent = $savedScore !== null
                && $recap->updated_at !== null
                && ($latestAssessmentAt === null || $recap->updated_at->greaterThanOrEqualTo($latestAssessmentAt));

            if ($savedScoreIsCurrent) {
                return [$student->id => (float) $savedScore];
            }

            return [$student->id => $autoScore !== null ? (float) $autoScore : null];
        });
    }

    /**
     * Get the most recent assessment timestamp for a class.
     *
     * @param  Collection<int, Model>  $dailyTestMeetings
     * @param  Collection<int, Model>  $assignmentMeetings
     * @param  Collection<int, Model>  $midtermExams
     * @param  Collection<int, Model>  $finalExams
     * @param  Collection<int, Collection<int, Model>>  $dailyScores
     * @param  Collection<int, Collection<int, Model>>  $assignmentScores
     * @param  Collection<int, Collection<int, Model>>  $midtermScores
     * @param  Collection<int, Collection<int, Model>>  $finalScores
     */
    private function latestAssessmentTimestamp(
        Collection $dailyTestMeetings,
        Collection $assignmentMeetings,
        Collection $midtermExams,
        Collection $finalExams,
        Collection $dailyScores,
        Collection $assignmentScores,
        Collection $midtermScores,
        Collection $finalScores,
    ): ?Carbon {
        $timestamps = collect([
            $dailyTestMeetings->max('updated_at'),
            $assignmentMeetings->max('updated_at'),
            $midtermExams->max('updated_at'),
            $finalExams->max('updated_at'),
            $dailyScores->flatten(1)->max('updated_at'),
            $assignmentScores->flatten(1)->max('updated_at'),
            $midtermScores->flatten(1)->max('updated_at'),
            $finalScores->flatten(1)->max('updated_at'),
        ])
            ->filter()
            ->map(fn ($timestamp): Carbon => $timestamp instanceof Carbon ? $timestamp : Carbon::parse($timestamp));

        return $timestamps->isEmpty() ? null : $timestamps->max();
    }
}
