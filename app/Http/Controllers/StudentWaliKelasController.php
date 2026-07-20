<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentWaliKelasController extends Controller
{
    /**
     * Display a listing of student wali kelas or render blade view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = StudentWaliKelas::with('classWaliKelas');

            if ($request->has('class_id')) {
                $query->where('class_id', $request->input('class_id'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->get();
        }

        return view('auth.waliKelas.siswa', compact('students', 'classWaliKelas'));
    }

    /**
     * Store a newly created student wali kelas in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $studentWaliKelas = StudentWaliKelas::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Student wali kelas created successfully.',
                'data' => $studentWaliKelas->load('classWaliKelas'),
            ], 201);
        }

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
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
    public function update(Request $request, StudentWaliKelas $studentWaliKelas): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules($studentWaliKelas->id));

        $studentWaliKelas->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Student wali kelas updated successfully.',
                'data' => $studentWaliKelas->load('classWaliKelas'),
            ]);
        }

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified student wali kelas from storage.
     */
    public function destroy(Request $request, StudentWaliKelas $studentWaliKelas): RedirectResponse|JsonResponse
    {
        $studentWaliKelas->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Student wali kelas deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Request $request, StudentWaliKelas $studentWaliKelas): RedirectResponse|JsonResponse
    {
        return $this->destroy($request, $studentWaliKelas);
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
