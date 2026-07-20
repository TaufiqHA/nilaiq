<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\Ekskul;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EkskulController extends Controller
{
    /**
     * Display a listing of the ekskuls or render blade view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = Ekskul::with('student');

            if ($request->has('student_id')) {
                $query->where('student_id', $request->input('student_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->with('ekskul')->get();
        }

        return view('auth.waliKelas.ekskul', compact('students', 'classWaliKelas'));
    }

    /**
     * Store a newly created or updated ekskul in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $ekskul = Ekskul::updateOrCreate(
            ['student_id' => $validated['student_id']],
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Ekskul created successfully.',
                'data' => $ekskul->load('student'),
            ], 201);
        }

        return redirect()->back()->with('success', 'Data ekstrakurikuler berhasil disimpan.');
    }

    /**
     * Display the specified ekskul.
     */
    public function show(Ekskul $ekskul): JsonResponse
    {
        return response()->json($ekskul->load('student'));
    }

    /**
     * Update the specified ekskul in storage.
     */
    public function update(Request $request, Ekskul $ekskul): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $ekskul->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Ekskul updated successfully.',
                'data' => $ekskul->load('student'),
            ]);
        }

        return redirect()->back()->with('success', 'Data ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Remove the specified ekskul from storage.
     */
    public function destroy(Request $request, Ekskul $ekskul): RedirectResponse|JsonResponse
    {
        $ekskul->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Ekskul deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Data ekstrakurikuler berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Request $request, Ekskul $ekskul): RedirectResponse|JsonResponse
    {
        return $this->destroy($request, $ekskul);
    }

    /**
     * Get the validation rules for the ekskul request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'student_id' => ['required', 'exists:student_wali_kelas,id'],
            'ekskul1' => ['nullable', 'string', 'max:255'],
            'ekskul2' => ['nullable', 'string', 'max:255'],
            'ekskul3' => ['nullable', 'string', 'max:255'],
        ];
    }
}
