<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\FinalExams;
use App\Rules\OwnedExists;
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
        $activeYear = AcademicYear::getActive();
        $exams = FinalExams::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->whereHas('class', fn ($class) => $class->where('academic_year_id', $activeYear->id)))
            ->with(['class.students', 'scores'])
            ->get();
        $classes = Classes::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->with('students')
            ->get();

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

        if (! empty($validated['client_uuid'])) {
            $existing = FinalExams::ownedBy(auth()->user())
                ->where('client_uuid', $validated['client_uuid'])
                ->first();

            if ($existing) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Final exam already exists.',
                        'data' => $existing->load('class'),
                    ]);
                }

                return redirect()->route('final-exams.index')->with('success', 'Ujian Akhir Semester sudah ada.');
            }
        }

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

        $activeYear = AcademicYear::getActive();
        $exams = FinalExams::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->whereHas('class', fn ($class) => $class->where('academic_year_id', $activeYear->id)))
            ->with(['class.students', 'scores'])
            ->get();
        $classes = Classes::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->with('students')
            ->get();

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
            'client_uuid' => ['nullable', 'uuid'],
            'class_id' => ['required', new OwnedExists(Classes::class)],
            'title' => ['required', 'string', 'max:255'],
            'exam_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
