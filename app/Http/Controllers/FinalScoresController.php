<?php

namespace App\Http\Controllers;

use App\Models\FinalScores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinalScoresController extends Controller
{
    /**
     * Display a listing of the final scores.
     */
    public function index(): JsonResponse
    {
        $scores = FinalScores::with(['finalExam', 'student'])->get();

        return response()->json($scores);
    }

    /**
     * Store a newly created final score in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        if (! empty($validated['client_uuid'])) {
            $existing = FinalScores::where('client_uuid', $validated['client_uuid'])->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Final score already exists.',
                    'data' => $existing->load(['finalExam', 'student']),
                ]);
            }
        }

        $score = FinalScores::updateOrCreate(
            [
                'final_exam_id' => $validated['final_exam_id'],
                'student_id' => $validated['student_id'],
            ],
            $validated
        );

        return response()->json([
            'message' => $score->wasRecentlyCreated
                ? 'Final score created successfully.'
                : 'Final score updated successfully.',
            'data' => $score->load(['finalExam', 'student']),
        ], $score->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified final score.
     */
    public function show(FinalScores $finalScore): JsonResponse
    {
        return response()->json($finalScore->load(['finalExam', 'student']));
    }

    /**
     * Update the specified final score in storage.
     */
    public function update(Request $request, FinalScores $finalScore): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $finalScore->update($validated);

        return response()->json([
            'message' => 'Final score updated successfully.',
            'data' => $finalScore->load(['finalExam', 'student']),
        ]);
    }

    /**
     * Remove the specified final score from storage.
     */
    public function destroy(FinalScores $finalScore): JsonResponse
    {
        $finalScore->delete();

        return response()->json([
            'message' => 'Final score deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(FinalScores $finalScore): JsonResponse
    {
        return $this->destroy($finalScore);
    }

    /**
     * Get the validation rules for the final score request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'client_uuid' => ['nullable', 'uuid'],
            'final_exam_id' => ['required', 'exists:final_exams,id'],
            'student_id' => ['required', 'exists:students,id'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
