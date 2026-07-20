<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClassWaliKelasController extends Controller
{
    /**
     * Display a listing of the class wali kelas.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ClassWaliKelas::with(['academicYear', 'user']);

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->input('academic_year_id'));
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created class wali kelas in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $classWaliKelas = ClassWaliKelas::create($validated);

        return response()->json([
            'message' => 'Class wali kelas created successfully.',
            'data' => $classWaliKelas->load(['academicYear', 'user']),
        ], 201);
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
    public function update(Request $request, ClassWaliKelas $classWaliKelas): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $classWaliKelas->update($validated);

        return response()->json([
            'message' => 'Class wali kelas updated successfully.',
            'data' => $classWaliKelas->load(['academicYear', 'user']),
        ]);
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
