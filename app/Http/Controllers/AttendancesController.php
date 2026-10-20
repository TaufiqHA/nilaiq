<?php

namespace App\Http\Controllers;

use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Students;
use App\Rules\OwnedExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendancesController extends Controller
{
    /**
     * Display a listing of the attendances.
     */
    public function index(): JsonResponse
    {
        $attendances = Attendances::ownedBy(auth()->user())
            ->with(['attendanceMeeting', 'student'])
            ->get();

        return response()->json($attendances);
    }

    /**
     * Store a newly created attendance in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $attendance = Attendances::updateOrCreate(
            [
                'attendance_meeting_id' => $validated['attendance_meeting_id'],
                'student_id' => $validated['student_id'],
            ],
            $validated
        );

        return response()->json([
            'message' => $attendance->wasRecentlyCreated
                ? 'Attendance created successfully.'
                : 'Attendance updated successfully.',
            'data' => $attendance->load(['attendanceMeeting', 'student']),
        ], $attendance->wasRecentlyCreated ? 201 : 200);
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
     * @return array<string, array<int, mixed>>
     */
    private function validationRules(): array
    {
        return [
            'client_uuid' => ['nullable', 'uuid'],
            'attendance_meeting_id' => ['required', new OwnedExists(AttendanceMeetings::class)],
            'student_id' => ['required', new OwnedExists(Students::class)],
            'status' => ['required', 'in:HADIR,IZIN,SAKIT,ALFA'],
            'note' => ['nullable', 'string'],
        ];
    }
}
