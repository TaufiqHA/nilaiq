<?php

namespace App\Http\Controllers;

use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\Students;
use App\Rules\OwnedExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyTestScoresController extends Controller
{
    /**
     * Display a listing of the daily test scores.
     */
    public function index(): JsonResponse
    {
        $scores = DailyTestScores::ownedBy(auth()->user())
            ->with(['dailyTestMeeting', 'student'])
            ->get();

        return response()->json($scores);
    }

    /**
     * Store a newly created daily test score in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        if (! empty($validated['client_uuid'])) {
            $existing = DailyTestScores::ownedBy(auth()->user())
                ->where('client_uuid', $validated['client_uuid'])
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Daily test score already exists.',
                    'data' => $existing->load(['dailyTestMeeting', 'student']),
                ]);
            }
        }

        $score = DailyTestScores::updateOrCreate(
            [
                'daily_test_meeting_id' => $validated['daily_test_meeting_id'],
                'student_id' => $validated['student_id'],
            ],
            $validated
        );

        return response()->json([
            'message' => $score->wasRecentlyCreated
                ? 'Daily test score created successfully.'
                : 'Daily test score updated successfully.',
            'data' => $score->load(['dailyTestMeeting', 'student']),
        ], $score->wasRecentlyCreated ? 201 : 200);
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
            'client_uuid' => ['nullable', 'uuid'],
            'daily_test_meeting_id' => ['required', new OwnedExists(DailyTestMeetings::class)],
            'student_id' => ['required', new OwnedExists(Students::class)],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
