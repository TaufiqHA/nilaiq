<?php

namespace App\Http\Controllers;

use App\Models\CatatanWaliKelas;
use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CatatanWaliKelasController extends Controller
{
    /**
     * Display a listing of the catatan wali kelas or render blade view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = CatatanWaliKelas::with('student');

            if ($request->has('student_id')) {
                $query->where('student_id', $request->input('student_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->with('catatanWaliKelas')->get();
        }

        return view('auth.waliKelas.catatanWaliKelas', compact('students', 'classWaliKelas'));
    }

    /**
     * Store a newly created or updated catatan wali kelas in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $catatanWaliKelas = CatatanWaliKelas::updateOrCreate(
            ['student_id' => $validated['student_id']],
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Catatan wali kelas created successfully.',
                'data' => $catatanWaliKelas->load('student'),
            ], 201);
        }

        return redirect()->back()->with('success', 'Data catatan wali kelas berhasil disimpan.');
    }

    /**
     * Display the specified catatan wali kelas.
     */
    public function show(CatatanWaliKelas $catatanWaliKelas): JsonResponse
    {
        return response()->json($catatanWaliKelas->load('student'));
    }

    /**
     * Update the specified catatan wali kelas in storage.
     */
    public function update(Request $request, CatatanWaliKelas $catatanWaliKelas): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $catatanWaliKelas->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Catatan wali kelas updated successfully.',
                'data' => $catatanWaliKelas->load('student'),
            ]);
        }

        return redirect()->back()->with('success', 'Data catatan wali kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified catatan wali kelas from storage.
     */
    public function destroy(Request $request, CatatanWaliKelas $catatanWaliKelas): RedirectResponse|JsonResponse
    {
        $catatanWaliKelas->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Catatan wali kelas deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Data catatan wali kelas berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Request $request, CatatanWaliKelas $catatanWaliKelas): RedirectResponse|JsonResponse
    {
        return $this->destroy($request, $catatanWaliKelas);
    }

    /**
     * Get the validation rules for the catatan wali kelas request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'student_id' => ['required', 'exists:student_wali_kelas,id'],
            'catatan' => ['nullable', 'string'],
        ];
    }
}
