<?php

namespace App\Http\Controllers;

use App\Models\AssignmentMeetings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentMeetingsController extends Controller
{
    /**
     * Display a listing of the assignment meetings.
     */
    public function index(): JsonResponse
    {
        $meetings = AssignmentMeetings::with('class.students')->get();

        return response()->json($meetings);
    }

    /**
     * Store a newly created assignment meeting in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $meeting = AssignmentMeetings::create($validated);

        return response()->json([
            'message' => 'Assignment meeting created successfully.',
            'data' => $meeting->load('class'),
        ], 201);
    }

    /**
     * Display the specified assignment meeting.
     */
    public function show(AssignmentMeetings $assignmentMeeting): JsonResponse
    {
        $assignmentMeeting->load('class.students');

        return response()->json($assignmentMeeting);
    }

    /**
     * Update the specified assignment meeting in storage.
     */
    public function update(Request $request, AssignmentMeetings $assignmentMeeting): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $assignmentMeeting->update($validated);

        return response()->json([
            'message' => 'Assignment meeting updated successfully.',
            'data' => $assignmentMeeting->load('class'),
        ]);
    }

    /**
     * Remove the specified assignment meeting from storage.
     */
    public function destroy(AssignmentMeetings $assignmentMeeting): JsonResponse
    {
        $assignmentMeeting->delete();

        return response()->json([
            'message' => 'Assignment meeting deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(AssignmentMeetings $assignmentMeeting): JsonResponse
    {
        return $this->destroy($assignmentMeeting);
    }

    /**
     * Get the validation rules for the assignment meeting request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'assignment_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
