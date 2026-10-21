<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ClassesController extends Controller
{
    /**
     * Display a listing of the classes.
     */
    public function index(Request $request): JsonResponse|View
    {
        $activeYear = AcademicYear::getActive();
        $classes = Classes::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->with(['academicYear', 'students'])
            ->get();

        if ($request->wantsJson()) {
            return response()->json($classes);
        }

        return view('auth.class', compact('classes', 'activeYear'));
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());
        $activeYear = AcademicYear::getActive();

        if (! $activeYear) {
            throw ValidationException::withMessages([
                'academic_year_id' => 'Aktifkan tahun ajaran terlebih dahulu sebelum menambahkan kelas.',
            ]);
        }

        $validated['academic_year_id'] = $activeYear->id;
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

        $activeYear = AcademicYear::getActive();
        $classes = Classes::ownedBy(auth()->user())
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->with(['academicYear', 'students'])
            ->get();

        return view('auth.class', compact('classes', 'activeYear', 'class'));
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, Classes $class): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

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
    private function validationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
