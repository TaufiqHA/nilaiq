<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\FinalExams;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinalExamsController extends Controller
{
    /**
     * Display a listing of the final exams.
     */
    public function index(Request $request): JsonResponse|View
    {
        $exams = FinalExams::with(['class.students', 'scores'])->get();
        $classes = Classes::with('students')->get();

        if ($request->wantsJson()) {
            return response()->json($exams);
        }

        return view('auth.pas', compact('exams', 'classes'));
    }

    /**
     * Store a newly created final exam in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $exam = FinalExams::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Final exam created successfully.',
                'data' => $exam->load('class'),
            ], 201);
        }

        return redirect()->route('final-exams.index')->with('success', 'Ujian Akhir Semester berhasil ditambahkan.');
    }

    /**
     * Display the specified final exam.
     */
    public function show(Request $request, FinalExams $finalExam): JsonResponse|View
    {
        $finalExam->load(['class.students', 'scores']);

        if ($request->wantsJson()) {
            return response()->json($finalExam);
        }

        $exams = FinalExams::with(['class.students', 'scores'])->get();
        $classes = Classes::with('students')->get();

        return view('auth.pas', compact('exams', 'classes', 'finalExam'));
    }

    /**
     * Update the specified final exam in storage.
     */
    public function update(Request $request, FinalExams $finalExam): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $finalExam->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Final exam updated successfully.',
                'data' => $finalExam->load('class'),
            ]);
        }

        return redirect()->route('final-exams.index')->with('success', 'Ujian Akhir Semester berhasil diperbarui.');
    }

    /**
     * Remove the specified final exam from storage.
     */
    public function destroy(FinalExams $finalExam): JsonResponse|RedirectResponse
    {
        $finalExam->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Final exam deleted successfully.',
            ]);
        }

        return redirect()->route('final-exams.index')->with('success', 'Ujian Akhir Semester berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(FinalExams $finalExam): JsonResponse|RedirectResponse
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
