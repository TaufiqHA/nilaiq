<?php

namespace App\Http\Controllers;

use App\Models\DailyTestScores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyTestScoresController extends Controller
{
    /**
     * Display a listing of the daily test scores.
     */
    public function index(): JsonResponse
    {
        $scores = DailyTestScores::with(['dailyTestMeeting', 'student'])->get();

        return response()->json($scores);
    }

    /**
     * Store a newly created daily test score in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $score = DailyTestScores::create($validated);

        return response()->json([
            'message' => 'Daily test score created successfully.',
            'data' => $score->load(['dailyTestMeeting', 'student']),
        ], 201);
    }

    /**
     * Display the specified daily test score.
     */
    public function show(DailyTestScores $dailyTestScore): JsonResponse
    {
        return response()->json($dailyTestScore->load(['dailyTestMeeting', 'student']));
    }

    /**
     * Update the specified daily test score in storage.
     */
    public function update(Request $request, DailyTestScores $dailyTestScore): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $dailyTestScore->update($validated);

        return response()->json([
            'message' => 'Daily test score updated successfully.',
            'data' => $dailyTestScore->load(['dailyTestMeeting', 'student']),
        ]);
    }

    /**
     * Remove the specified daily test score from storage.
     */
    public function destroy(DailyTestScores $dailyTestScore): JsonResponse
    {
        $dailyTestScore->delete();

        return response()->json([
            'message' => 'Daily test score deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(DailyTestScores $dailyTestScore): JsonResponse
    {
        return $this->destroy($dailyTestScore);
    }

    /**
     * Get the validation rules for the daily test score request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'daily_test_meeting_id' => ['required', 'exists:daily_test_meetings,id'],
            'student_id' => ['required', 'exists:students,id'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
