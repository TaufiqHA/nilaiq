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

        $score = MidtermScores::create($validated);

        return response()->json([
            'message' => 'Midterm score created successfully.',
            'data' => $score->load(['midtermExam', 'student']),
        ], 201);
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
            'midterm_exam_id' => ['required', 'exists:midterm_exams,id'],
            'student_id' => ['required', 'exists:students,id'],
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
