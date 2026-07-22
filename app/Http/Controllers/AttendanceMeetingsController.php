<?php

namespace App\Http\Controllers;

use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\Settings;
use App\Models\Students;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceMeetingsController extends Controller
{
    /**
     * Display a listing of the attendance meetings.
     */
    public function index(Request $request): JsonResponse|View
    {
        $meetings = AttendanceMeetings::with(['class.students', 'attendances'])->get();
        $classes = Classes::with('students')->get();

        if ($request->wantsJson()) {
            return response()->json($meetings);
        }

        return view('auth.absensi', compact('meetings', 'classes'));
    }

    /**
     * Store a newly created attendance meeting in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $meeting = AttendanceMeetings::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Attendance meeting created successfully.',
                'data' => $meeting->load('class'),
            ], 201);
        }

        return redirect()->route('attendance-meetings.index')->with('success', 'Attendance meeting created successfully.');
    }

    /**
     * Display the specified attendance meeting.
     */
    public function show(Request $request, AttendanceMeetings $attendanceMeeting): JsonResponse|View
    {
        $attendanceMeeting->load(['class.students', 'attendances']);

        if ($request->wantsJson()) {
            return response()->json($attendanceMeeting);
        }

        $meetings = AttendanceMeetings::with(['class.students', 'attendances'])->get();
        $classes = Classes::with('students')->get();

        return view('auth.absensi', compact('meetings', 'classes', 'attendanceMeeting'));
    }

    /**
     * Update the specified attendance meeting in storage.
     */
    public function update(Request $request, AttendanceMeetings $attendanceMeeting): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $attendanceMeeting->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Attendance meeting updated successfully.',
                'data' => $attendanceMeeting->load('class'),
            ]);
        }

        return redirect()->route('attendance-meetings.index')->with('success', 'Attendance meeting updated successfully.');
    }

    /**
     * Remove the specified attendance meeting from storage.
     */
    public function destroy(AttendanceMeetings $attendanceMeeting): JsonResponse|RedirectResponse
    {
        $attendanceMeeting->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Attendance meeting deleted successfully.',
            ]);
        }

        return redirect()->route('attendance-meetings.index')->with('success', 'Attendance meeting deleted successfully.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(AttendanceMeetings $attendanceMeeting): JsonResponse|RedirectResponse
    {
        return $this->destroy($attendanceMeeting);
    }

    /**
     * Get the validation rules for the attendance meeting request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'tipe' => ['required', 'string', 'in:harian,ulang harian,tugas,pts,pas'],
        ];
    }

    /**
     * Display a recap of the attendance meetings for a class.
     */
    public function rekapAbsensi(Request $request): View
    {
        $classId = $request->query('class_id');
        $classes = Classes::all();

        $selectedClass = null;
        $students = collect();
        $meetings = collect();
        $attendances = collect();

        // Scores data
        $dailyTests = collect();
        $assignments = collect();
        $midterms = collect();
        $finals = collect();
        $dailyScores = collect();
        $assignmentScores = collect();
        $midtermScores = collect();
        $finalScores = collect();

        if ($classId) {
            $selectedClass = Classes::with('academicYear')->find($classId);
            if ($selectedClass) {
                $students = Students::where('class_id', $classId)
                    ->where('status', 'ACTIVE')
                    ->orderBy('name', 'asc')
                    ->get();

                // Get all attendance meetings for this class ordered by date and ID
                $meetings = AttendanceMeetings::where('class_id', $classId)
                    ->orderBy('meeting_date', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();

                // Get attendances for these meetings
                $attendances = Attendances::whereIn('attendance_meeting_id', $meetings->pluck('id'))
                    ->get()
                    ->groupBy('student_id');

                // Get assessment data for the columns UH1-UH3, T1-T3, PTS, PAS
                $dailyTests = DailyTestMeetings::where('class_id', $classId)->orderBy('id')->get();
                $assignments = AssignmentMeetings::where('class_id', $classId)->orderBy('id')->get();
                $midterms = MidtermExams::where('class_id', $classId)->orderBy('id')->take(1)->get();
                $finals = FinalExams::where('class_id', $classId)->orderBy('id')->take(1)->get();

                $dailyScores = DailyTestScores::whereIn('daily_test_meeting_id', $dailyTests->pluck('id'))
                    ->get()
                    ->groupBy('student_id');

                $assignmentScores = AssignmentScores::whereIn('assignment_meeting_id', $assignments->pluck('id'))
                    ->get()
                    ->groupBy('student_id');

                $midtermScores = MidtermScores::whereIn('midterm_exam_id', $midterms->pluck('id'))
                    ->get()
                    ->groupBy('student_id');

                $finalScores = FinalScores::whereIn('final_exam_id', $finals->pluck('id'))
                    ->get()
                    ->groupBy('student_id');
            }
        }

        $settings = Settings::first();

        return view('auth.rekapAbsensi', compact(
            'classes',
            'selectedClass',
            'students',
            'meetings',
            'attendances',
            'dailyTests',
            'assignments',
            'midterms',
            'finals',
            'dailyScores',
            'assignmentScores',
            'midtermScores',
            'finalScores',
            'settings'
        ));
    }
}
