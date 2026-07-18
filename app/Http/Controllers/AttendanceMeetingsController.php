<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMeetings;
use App\Models\Classes;
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
        ];
    }
}
