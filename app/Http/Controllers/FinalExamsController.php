<?php

namespace App\Http\Controllers;

use App\Models\FinalExams;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinalExamsController extends Controller
{
    /**
     * Display a listing of the final exams.
     */
    public function index(Request $request): JsonResponse
    {
        $exams = FinalExams::with('class')->get();

        return response()->json($exams);
    }

    /**
     * Store a newly created final exam in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $exam = FinalExams::create($validated);

        return response()->json([
            'message' => 'Final exam created successfully.',
            'data' => $exam->load('class'),
        ], 201);
    }

    /**
     * Display the specified final exam.
     */
    public function show(Request $request, FinalExams $finalExam): JsonResponse
    {
        $finalExam->load('class');

        return response()->json($finalExam);
    }

    /**
     * Update the specified final exam in storage.
     */
    public function update(Request $request, FinalExams $finalExam): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $finalExam->update($validated);

        return response()->json([
            'message' => 'Final exam updated successfully.',
            'data' => $finalExam->load('class'),
        ]);
    }

    /**
     * Remove the specified final exam from storage.
     */
    public function destroy(FinalExams $finalExam): JsonResponse
    {
        $finalExam->delete();

        return response()->json([
            'message' => 'Final exam deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(FinalExams $finalExam): JsonResponse
    {
        return $this->destroy($finalExam);
    }

    /**
     * Get the validation rules for the final exam request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:255'],
            'exam_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
