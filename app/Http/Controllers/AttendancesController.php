<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendancesController extends Controller
{
    /**
     * Display a listing of the attendances.
     */
    public function index(): JsonResponse
    {
        $attendances = Attendances::with(['attendanceMeeting', 'student'])->get();

        return response()->json($attendances);
    }

    /**
     * Store a newly created attendance in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $attendance = Attendances::create($validated);

        return response()->json([
            'message' => 'Attendance created successfully.',
            'data' => $attendance->load(['attendanceMeeting', 'student']),
        ], 201);
    }

    /**
     * Display the specified attendance.
     */
    public function show(Attendances $attendance): JsonResponse
    {
        return response()->json($attendance->load(['attendanceMeeting', 'student']));
    }

    /**
     * Update the specified attendance in storage.
     */
    public function update(Request $request, Attendances $attendance): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $attendance->update($validated);

        return response()->json([
            'message' => 'Attendance updated successfully.',
            'data' => $attendance->load(['attendanceMeeting', 'student']),
        ]);
    }

    /**
     * Remove the specified attendance from storage.
     */
    public function destroy(Attendances $attendance): JsonResponse
    {
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Attendances $attendance): JsonResponse
    {
        return $this->destroy($attendance);
    }

    /**
     * Get the validation rules for the attendance request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'attendance_meeting_id' => ['required', 'exists:attendance_meetings,id'],
            'student_id' => ['required', 'exists:students,id'],
            'status' => ['required', 'in:HADIR,IZIN,SAKIT,ALFA'],
            'note' => ['nullable', 'string'],
        ];
    }
}
