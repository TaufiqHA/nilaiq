<?php

namespace App\Http\Controllers;

use App\Models\AssignmentScores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentScoresController extends Controller
{
    /**
     * Display a listing of the assignment scores.
     */
    public function index(): JsonResponse
    {
        $scores = AssignmentScores::with(['assignmentMeeting', 'student'])->get();

        return response()->json($scores);
    }

    /**
     * Store a newly created assignment score in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $score = AssignmentScores::create($validated);

        return response()->json([
            'message' => 'Assignment score created successfully.',
            'data' => $score->load(['assignmentMeeting', 'student']),
        ], 201);
    }

    /**
     * Display the specified assignment score.
     */
    public function show(AssignmentScores $assignmentScore): JsonResponse
    {
        return response()->json($assignmentScore->load(['assignmentMeeting', 'student']));
    }

    /**
     * Update the specified assignment score in storage.
     */
    public function update(Request $request, AssignmentScores $assignmentScore): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $assignmentScore->update($validated);

        return response()->json([
            'message' => 'Assignment score updated successfully.',
            'data' => $assignmentScore->load(['assignmentMeeting', 'student']),
        ]);
    }

    /**
     * Remove the specified assignment score from storage.
     */
    public function destroy(AssignmentScores $assignmentScore): JsonResponse
    {
        $assignmentScore->delete();

        return response()->json([
            'message' => 'Assignment score deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(AssignmentScores $assignmentScore): JsonResponse
    {
        return $this->destroy($assignmentScore);
    }

    /**
     * Get the validation rules for the assignment score request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'assignment_meeting_id' => ['required', 'exists:assignment_meetings,id'],
            'student_id' => ['required', 'exists:students,id'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
