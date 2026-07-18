<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\MidtermExams;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MidtermExamsController extends Controller
{
    /**
     * Display a listing of the midterm exams.
     */
    public function index(Request $request): JsonResponse|View
    {
        $exams = MidtermExams::with(['class.students', 'scores'])->get();
        $classes = Classes::with('students')->get();

        if ($request->wantsJson()) {
            return response()->json($exams);
        }

        return view('auth.pts', compact('exams', 'classes'));
    }

    /**
     * Store a newly created midterm exam in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $exam = MidtermExams::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Midterm exam created successfully.',
                'data' => $exam->load('class'),
            ], 201);
        }

        return redirect()->route('midterm-exams.index')->with('success', 'Ujian Tengah Semester berhasil ditambahkan.');
    }

    /**
     * Display the specified midterm exam.
     */
    public function show(Request $request, MidtermExams $midtermExam): JsonResponse|View
    {
        $midtermExam->load(['class.students', 'scores']);

        if ($request->wantsJson()) {
            return response()->json($midtermExam);
        }

        $exams = MidtermExams::with(['class.students', 'scores'])->get();
        $classes = Classes::with('students')->get();

        return view('auth.pts', compact('exams', 'classes', 'midtermExam'));
    }

    /**
     * Update the specified midterm exam in storage.
     */
    public function update(Request $request, MidtermExams $midtermExam): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $midtermExam->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Midterm exam updated successfully.',
                'data' => $midtermExam->load('class'),
            ]);
        }

        return redirect()->route('midterm-exams.index')->with('success', 'Ujian Tengah Semester berhasil diperbarui.');
    }

    /**
     * Remove the specified midterm exam from storage.
     */
    public function destroy(MidtermExams $midtermExam): JsonResponse|RedirectResponse
    {
        $midtermExam->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Midterm exam deleted successfully.',
            ]);
        }

        return redirect()->route('midterm-exams.index')->with('success', 'Ujian Tengah Semester berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(MidtermExams $midtermExam): JsonResponse|RedirectResponse
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
