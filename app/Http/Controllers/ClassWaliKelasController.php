<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClassWaliKelasController extends Controller
{
    /**
     * Display a listing of the class wali kelas or render class info page.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = ClassWaliKelas::with(['academicYear', 'user']);

            if ($request->has('academic_year_id')) {
                $query->where('academic_year_id', $request->input('academic_year_id'));
            }

            if ($request->has('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::with(['academicYear', 'user'])
            ->where('user_id', $userId)
            ->first();

        $academicYears = AcademicYear::where('user_id', $userId)->get();
        if ($academicYears->isEmpty()) {
            $academicYears = AcademicYear::all();
        }

        return view('auth.waliKelas.kelas', compact('classWaliKelas', 'academicYears'));
    }

    /**
     * Store a newly created class wali kelas in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $classWaliKelas = ClassWaliKelas::updateOrCreate(
            ['user_id' => $validated['user_id']],
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Class wali kelas created successfully.',
                'data' => $classWaliKelas->load(['academicYear', 'user']),
            ], 201);
        }

        return redirect()->back()->with('success', 'Informasi kelas berhasil disimpan.');
    }

    /**
     * Display the specified class wali kelas.
     */
    public function show(ClassWaliKelas $classWaliKelas): JsonResponse
    {
        return response()->json($classWaliKelas->load(['academicYear', 'user']));
    }

    /**
     * Update the specified class wali kelas in storage.
     */
    public function update(Request $request, ClassWaliKelas $classWaliKelas): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $classWaliKelas->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Class wali kelas updated successfully.',
                'data' => $classWaliKelas->load(['academicYear', 'user']),
            ]);
        }

        return redirect()->back()->with('success', 'Informasi kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified class wali kelas from storage.
     */
    public function destroy(ClassWaliKelas $classWaliKelas): JsonResponse
    {
        $classWaliKelas->delete();

        return response()->json([
            'message' => 'Class wali kelas deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(ClassWaliKelas $classWaliKelas): JsonResponse
    {
        return $this->destroy($classWaliKelas);
    }

    /**
     * Get the validation rules for class wali kelas request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
