<?php

namespace App\Http\Controllers;

use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\Students;
use App\Rules\OwnedExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssignmentScoresController extends Controller
{
    /**
     * Display a listing of the assignment scores.
     */
    public function index(): JsonResponse
    {
        $scores = AssignmentScores::ownedBy(auth()->user())
            ->with(['assignmentMeeting', 'student'])
            ->get();

        return response()->json($scores);
    }

    /**
     * Store a newly created assignment score in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        if (! empty($validated['client_uuid'])) {
            $existing = AssignmentScores::ownedBy(auth()->user())
                ->where('client_uuid', $validated['client_uuid'])
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Assignment score already exists.',
                    'data' => $existing->load(['assignmentMeeting', 'student']),
                ]);
            }
        }

        $score = AssignmentScores::updateOrCreate(
            [
                'assignment_meeting_id' => $validated['assignment_meeting_id'],
                'student_id' => $validated['student_id'],
            ],
            $validated
        );

        return response()->json([
            'message' => $score->wasRecentlyCreated
                ? 'Assignment score created successfully.'
                : 'Assignment score updated successfully.',
            'data' => $score->load(['assignmentMeeting', 'student']),
        ], $score->wasRecentlyCreated ? 201 : 200);
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
            'client_uuid' => ['nullable', 'uuid'],
            'assignment_meeting_id' => ['required', new OwnedExists(AssignmentMeetings::class)],
            'student_id' => ['required', new OwnedExists(Students::class)],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
