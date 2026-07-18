<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMeetings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceMeetingsController extends Controller
{
    /**
     * Display a listing of the attendance meetings.
     */
    public function index(): JsonResponse
    {
        $meetings = AttendanceMeetings::with('class')->get();

        return response()->json($meetings);
    }

    /**
     * Store a newly created attendance meeting in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $meeting = AttendanceMeetings::create($validated);

        return response()->json([
            'message' => 'Attendance meeting created successfully.',
            'data' => $meeting->load('class'),
        ], 201);
    }

    /**
     * Display the specified attendance meeting.
     */
    public function show(AttendanceMeetings $attendanceMeeting): JsonResponse
    {
        return response()->json($attendanceMeeting->load('class'));
    }

    /**
     * Update the specified attendance meeting in storage.
     */
    public function update(Request $request, AttendanceMeetings $attendanceMeeting): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $attendanceMeeting->update($validated);

        return response()->json([
            'message' => 'Attendance meeting updated successfully.',
            'data' => $attendanceMeeting->load('class'),
        ]);
    }

    /**
     * Remove the specified attendance meeting from storage.
     */
    public function destroy(AttendanceMeetings $attendanceMeeting): JsonResponse
    {
        $attendanceMeeting->delete();

        return response()->json([
            'message' => 'Attendance meeting deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(AttendanceMeetings $attendanceMeeting): JsonResponse
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
