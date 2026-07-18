<?php

namespace App\Http\Controllers;

use App\Models\Students;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(): JsonResponse
    {
        $students = Students::with('class')->get();

        return response()->json($students);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $student = Students::create($validated);

        return response()->json([
            'message' => 'Student created successfully.',
            'data' => $student->load('class'),
        ], 201);
    }

    /**
     * Display the specified student.
     */
    public function show(Students $student): JsonResponse
    {
        return response()->json($student->load('class'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Students $student): JsonResponse
    {
        $validated = $request->validate($this->validationRules($student->id));

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully.',
            'data' => $student->load('class'),
        ]);
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Students $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Students $student): JsonResponse
    {
        return $this->destroy($student);
    }

    /**
     * Get the validation rules for the students request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(?int $studentId = null): array
    {
        return [
            'class_id' => ['required', 'exists:classes,id'],
            'nis' => ['required', 'string', 'max:50', 'unique:students,nis'.($studentId ? ','.$studentId : '')],
            'nisn' => ['required', 'string', 'max:50', 'unique:students,nisn'.($studentId ? ','.$studentId : '')],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'in:L,P'],
            'birth_place' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'address' => ['required', 'string'],
            'parent_name' => ['required', 'string', 'max:255'],
            'parent_phone' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
