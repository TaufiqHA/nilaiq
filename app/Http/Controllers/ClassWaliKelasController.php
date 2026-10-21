<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ClassWaliKelasController extends Controller
{
    /**
     * Display a listing of the class wali kelas or render class info page.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = ClassWaliKelas::ownedBy(auth()->user())->with(['academicYear', 'user']);

            if ($request->has('academic_year_id')) {
                $query->where('academic_year_id', $request->input('academic_year_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $activeAcademicYear = AcademicYear::getActive();
        $classWaliKelas = $activeAcademicYear
            ? ClassWaliKelas::with(['academicYear', 'user'])
                ->where('user_id', $userId)
                ->where('academic_year_id', $activeAcademicYear->id)
                ->first()
            : ClassWaliKelas::with(['academicYear', 'user'])
                ->where('user_id', $userId)
                ->first();

        return view('auth.waliKelas.kelas', compact('classWaliKelas', 'activeAcademicYear'));
    }

    /**
     * Store a newly created class wali kelas in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());
        $activeAcademicYear = AcademicYear::getActive();

        if (! $activeAcademicYear) {
            throw ValidationException::withMessages([
                'academic_year_id' => 'Aktifkan tahun ajaran terlebih dahulu sebelum menyimpan informasi kelas.',
            ]);
        }

        $validated['user_id'] = auth()->id();
        $validated['academic_year_id'] = $activeAcademicYear->id;

        $classWaliKelas = ClassWaliKelas::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'academic_year_id' => $validated['academic_year_id'],
            ],
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
        $validated['user_id'] = auth()->id();

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
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
