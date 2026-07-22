<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassesController extends Controller
{
    /**
     * Display a listing of the classes.
     */
    public function index(Request $request): JsonResponse|View
    {
        $classes = Classes::with(['academicYear', 'students'])->get();
        $academicYears = AcademicYear::where('user_id', auth()->id())
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'asc')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($classes);
        }

        return view('auth.class', compact('classes', 'academicYears'));
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $class = Classes::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Class created successfully.',
                'data' => $class->load('academicYear'),
            ], 201);
        }

        return redirect()->route('classes.index')->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified class.
     */
    public function show(Request $request, Classes $class): JsonResponse|View
    {
        $class->load(['academicYear', 'students']);

        if ($request->wantsJson()) {
            return response()->json($class);
        }

        $classes = Classes::with(['academicYear', 'students'])->get();
        $academicYears = AcademicYear::where('user_id', auth()->id())
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'asc')
            ->get();

        return view('auth.class', compact('classes', 'academicYears', 'class'));
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, Classes $class): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules($class->id));

        $class->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Class updated successfully.',
                'data' => $class->load('academicYear'),
            ]);
        }

        return redirect()->route('classes.index')->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy(Classes $class): JsonResponse|RedirectResponse
    {
        $class->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Class deleted successfully.',
            ]);
        }

        return redirect()->route('classes.index')->with('success', 'Class deleted successfully.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Classes $class): JsonResponse|RedirectResponse
    {
        return $this->destroy($class);
    }

    /**
     * Get the validation rules for the class request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(?int $classId = null): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
