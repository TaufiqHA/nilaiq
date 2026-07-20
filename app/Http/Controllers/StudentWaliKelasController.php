<?php

namespace App\Http\Controllers;

use App\Models\StudentWaliKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentWaliKelasController extends Controller
{
    /**
     * Display a listing of student wali kelas.
     */
    public function index(Request $request): JsonResponse
    {
        $query = StudentWaliKelas::with('classWaliKelas');

        if ($request->has('class_id')) {
            $query->where('class_id', $request->input('class_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created student wali kelas in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $studentWaliKelas = StudentWaliKelas::create($validated);

        return response()->json([
            'message' => 'Student wali kelas created successfully.',
            'data' => $studentWaliKelas->load('classWaliKelas'),
        ], 201);
    }

    /**
     * Display the specified student wali kelas.
     */
    public function show(StudentWaliKelas $studentWaliKelas): JsonResponse
    {
        return response()->json($studentWaliKelas->load('classWaliKelas'));
    }

    /**
     * Update the specified student wali kelas in storage.
     */
    public function update(Request $request, StudentWaliKelas $studentWaliKelas): JsonResponse
    {
        $validated = $request->validate($this->validationRules($studentWaliKelas->id));

        $studentWaliKelas->update($validated);

        return response()->json([
            'message' => 'Student wali kelas updated successfully.',
            'data' => $studentWaliKelas->load('classWaliKelas'),
        ]);
    }

    /**
     * Remove the specified student wali kelas from storage.
     */
    public function destroy(StudentWaliKelas $studentWaliKelas): JsonResponse
    {
        $studentWaliKelas->delete();

        return response()->json([
            'message' => 'Student wali kelas deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(StudentWaliKelas $studentWaliKelas): JsonResponse
    {
        return $this->destroy($studentWaliKelas);
    }

    /**
     * Get the validation rules for student wali kelas request.
     *
     * @return array<string, array<int, mixed>>
     */
    private function validationRules(?int $ignoreId = null): array
    {
        return [
            'class_id' => ['required', 'exists:class_wali_kelas,id'],
            'nis' => ['required', 'string', 'max:255', Rule::unique('student_wali_kelas', 'nis')->ignore($ignoreId)],
            'nisn' => ['nullable', 'string', 'max:255', Rule::unique('student_wali_kelas', 'nisn')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', Rule::in(['L', 'P'])],
            'birth_place' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'religion' => ['required', 'string', 'max:255'],
            'family_status' => ['required', 'string', 'max:255'],
            'child_order' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'previous_school' => ['nullable', 'string', 'max:255'],
            'registration_status' => ['nullable', 'string', 'max:255'],
            'accepted_class' => ['nullable', 'string', 'max:255'],
            'accepted_date' => ['nullable', 'date'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'father_job' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_job' => ['nullable', 'string', 'max:255'],
            'parent_address' => ['nullable', 'string'],
            'parent_phone' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_job' => ['nullable', 'string', 'max:255'],
            'guardian_address' => ['nullable', 'string'],
            'guardian_phone' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['ACTIVE', 'INACTIVE'])],
        ];
    }
}
