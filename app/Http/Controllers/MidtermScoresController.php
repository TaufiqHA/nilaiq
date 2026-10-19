<?php

namespace App\Http\Controllers;

use App\Models\MidtermScores;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtermScoresController extends Controller
{
    /**
     * Display a listing of the midterm scores.
     */
    public function index(): JsonResponse
    {
        $scores = MidtermScores::with(['midtermExam', 'student'])->get();

        return response()->json($scores);
    }

    /**
     * Store a newly created midterm score in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        if (! empty($validated['client_uuid'])) {
            $existing = MidtermScores::where('client_uuid', $validated['client_uuid'])->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Midterm score already exists.',
                    'data' => $existing->load(['midtermExam', 'student']),
                ]);
            }
        }

        $score = MidtermScores::updateOrCreate(
            [
                'midterm_exam_id' => $validated['midterm_exam_id'],
                'student_id' => $validated['student_id'],
            ],
            $validated
        );

        return response()->json([
            'message' => $score->wasRecentlyCreated
                ? 'Midterm score created successfully.'
                : 'Midterm score updated successfully.',
            'data' => $score->load(['midtermExam', 'student']),
        ], $score->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Display the specified midterm score.
     */
    public function show(MidtermScores $midtermScore): JsonResponse
    {
        return response()->json($midtermScore->load(['midtermExam', 'student']));
    }

    /**
     * Update the specified midterm score in storage.
     */
    public function update(Request $request, MidtermScores $midtermScore): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $midtermScore->update($validated);

        return response()->json([
            'message' => 'Midterm score updated successfully.',
            'data' => $midtermScore->load(['midtermExam', 'student']),
        ]);
    }

    /**
     * Remove the specified midterm score from storage.
     */
    public function destroy(MidtermScores $midtermScore): JsonResponse
    {
        $midtermScore->delete();

        return response()->json([
            'message' => 'Midterm score deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(MidtermScores $midtermScore): JsonResponse
    {
        return $this->destroy($midtermScore);
    }

    /**
     * Get the validation rules for the midterm score request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'client_uuid' => ['nullable', 'uuid'],
            'midterm_exam_id' => ['required', 'exists:midterm_exams,id'],
            'student_id' => ['required', 'exists:students,id'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
