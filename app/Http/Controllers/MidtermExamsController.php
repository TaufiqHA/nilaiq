<?php

namespace App\Http\Controllers;

use App\Models\MidtermExams;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtermExamsController extends Controller
{
    /**
     * Display a listing of the midterm exams.
     */
    public function index(): JsonResponse
    {
        $exams = MidtermExams::with(['class.students'])->get();

        return response()->json($exams);
    }

    /**
     * Store a newly created midterm exam in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $exam = MidtermExams::create($validated);

        return response()->json([
            'message' => 'Midterm exam created successfully.',
            'data' => $exam->load('class'),
        ], 201);
    }

    /**
     * Display the specified midterm exam.
     */
    public function show(MidtermExams $midtermExam): JsonResponse
    {
        $midtermExam->load(['class.students']);

        return response()->json($midtermExam);
    }

    /**
     * Update the specified midterm exam in storage.
     */
    public function update(Request $request, MidtermExams $midtermExam): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $midtermExam->update($validated);

        return response()->json([
            'message' => 'Midterm exam updated successfully.',
            'data' => $midtermExam->load('class'),
        ]);
    }

    /**
     * Remove the specified midterm exam from storage.
     */
    public function destroy(MidtermExams $midtermExam): JsonResponse
    {
        $midtermExam->delete();

        return response()->json([
            'message' => 'Midterm exam deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(MidtermExams $midtermExam): JsonResponse
    {
        return $this->destroy($midtermExam);
    }

    /**
     * Get the validation rules for the midterm exam request.
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
