<?php

namespace App\Http\Controllers;

use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\Classes;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class RekapController extends Controller
{
    /**
     * Display a listing of classes for the score recap.
     */
    public function index(): View
    {
        $classes = Classes::with(['academicYear', 'students'])->get();
        $settings = Settings::first();

        return view('auth.rekap', compact('classes', 'settings'));
    }

    /**
     * Get the aggregated score recap data for a specific class with split columns for meetings.
     */
    public function getClassRekapData(Classes $class): JsonResponse
    {
        $classId = $class->id;
        $students = $class->students()->where('status', 'ACTIVE')->get();

        // Get all meetings/exams for the class in chronological order
        $dailyTestMeetings = DailyTestMeetings::where('class_id', $classId)->orderBy('id')->get();
        $assignmentMeetings = AssignmentMeetings::where('class_id', $classId)->orderBy('id')->get();
        $midtermExams = MidtermExams::where('class_id', $classId)->orderBy('id')->get();
        $finalExams = FinalExams::where('class_id', $classId)->orderBy('id')->get();

        // Fetch scores
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

        $rekapData = [];
        foreach ($students as $student) {
            $studentScores = [
                'daily_tests' => [],
                'assignments' => [],
                'midterms' => [],
                'finals' => [],
            ];

            $totalScore = 0;

            // Map Daily Test scores
            $studentDaily = $dailyScores->get($student->id) ?? collect();
            foreach ($dailyTestMeetings as $meeting) {
                $scoreObj = $studentDaily->firstWhere('daily_test_meeting_id', $meeting->id);
                $scoreVal = $scoreObj ? floatval($scoreObj->score) : 0;
                $studentScores['daily_tests'][$meeting->id] = $scoreVal;
                $totalScore += $scoreVal;
            }

            // Map Assignment scores
            $studentAssignment = $assignmentScores->get($student->id) ?? collect();
            foreach ($assignmentMeetings as $meeting) {
                $scoreObj = $studentAssignment->firstWhere('assignment_meeting_id', $meeting->id);
                $scoreVal = $scoreObj ? floatval($scoreObj->score) : 0;
                $studentScores['assignments'][$meeting->id] = $scoreVal;
                $totalScore += $scoreVal;
            }

            // Map Midterm scores
            $studentMidterm = $midtermScores->get($student->id) ?? collect();
            foreach ($midtermExams as $exam) {
                $scoreObj = $studentMidterm->firstWhere('midterm_exam_id', $exam->id);
                $scoreVal = $scoreObj ? floatval($scoreObj->score) : 0;
                $studentScores['midterms'][$exam->id] = $scoreVal;
                $totalScore += $scoreVal;
            }

            // Map Final scores
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

        // Sort by rata_rata descending, and alphabetically by name as secondary sort for ties
        usort($rekapData, function (array $a, array $b): int {
            if ($b['rata_rata'] === $a['rata_rata']) {
                return $a['student']->name <=> $b['student']->name;
            }

            return $b['rata_rata'] <=> $a['rata_rata'];
        });

        // Assign rankings
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

        return response()->json([
            'class' => $class->load('academicYear'),
            'meetings' => [
                'daily_tests' => $dailyTestMeetings,
                'assignments' => $assignmentMeetings,
                'midterms' => $midtermExams,
                'finals' => $finalExams,
            ],
            'rekap' => $rekapData,
        ]);
    }
}
