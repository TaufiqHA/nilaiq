<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the academic years.
     */
    public function index(Request $request): JsonResponse|View
    {
        $academicYears = AcademicYear::where('user_id', auth()->id())
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'asc')
            ->get();

        if ($request->wantsJson()) {
            return response()->json($academicYears);
        }

        return view('auth.AcademicYear', compact('academicYears'));
    }

    /**
     * Store a newly created academic year in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $validated['is_active'] = $request->boolean('is_active');
        $validated['user_id'] = auth()->id();

        if ($validated['is_active']) {
            AcademicYear::where('user_id', auth()->id())->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Academic year created successfully.',
                'data' => $academicYear,
            ], 201);
        }

        return redirect()->route('academic-years.index')->with('success', 'Academic year created successfully.');
    }

    /**
     * Display the specified academic year.
     */
    public function show(Request $request, AcademicYear $academicYear): JsonResponse|View
    {
        if ($academicYear->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($request->wantsJson()) {
            return response()->json($academicYear);
        }

        $academicYears = AcademicYear::where('user_id', auth()->id())
            ->orderBy('year', 'desc')
            ->orderBy('semester', 'asc')
            ->get();

        return view('auth.AcademicYear', compact('academicYears', 'academicYear'));
    }

    /**
     * Update the specified academic year in storage.
     */
    public function update(Request $request, AcademicYear $academicYear): JsonResponse|RedirectResponse
    {
        if ($academicYear->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate($this->validationRules());

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            AcademicYear::where('user_id', auth()->id())
                ->where('id', '!=', $academicYear->id)
                ->update(['is_active' => false]);
        }

        $academicYear->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Academic year updated successfully.',
                'data' => $academicYear,
            ]);
        }

        return redirect()->route('academic-years.index')->with('success', 'Academic year updated successfully.');
    }

    /**
     * Remove the specified academic year from storage.
     */
    public function destroy(AcademicYear $academicYear): JsonResponse|RedirectResponse
    {
        if ($academicYear->user_id !== auth()->id()) {
            abort(403, 'Akses ditolak.');
        }

        $academicYear->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Academic year deleted successfully.',
            ]);
        }

        return redirect()->route('academic-years.index')->with('success', 'Academic year deleted successfully.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(AcademicYear $academicYear): JsonResponse|RedirectResponse
    {
        return $this->destroy($academicYear);
    }

    /**
     * Get the validation rules for the academic year request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'year' => ['required', 'string', 'max:50'],
            'semester' => ['required', 'string', 'in:GANJIL,GENAP'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
