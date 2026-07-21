<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the absensis or render blade view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = Absensi::with('student');

            if ($request->has('student_id')) {
                $query->where('student_id', $request->input('student_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->with('absensi')->get();
        }

        return view('auth.waliKelas.absensi', compact('students', 'classWaliKelas'));
    }

    /**
     * Store a newly created or updated absensi in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $absensi = Absensi::updateOrCreate(
            ['student_id' => $validated['student_id']],
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Absensi created successfully.',
                'data' => $absensi->load('student'),
            ], 201);
        }

        return redirect()->back()->with('success', 'Data absensi berhasil disimpan.');
    }

    /**
     * Display the specified absensi.
     */
    public function show(Absensi $absensi): JsonResponse
    {
        return response()->json($absensi->load('student'));
    }

    /**
     * Update the specified absensi in storage.
     */
    public function update(Request $request, Absensi $absensi): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $absensi->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Absensi updated successfully.',
                'data' => $absensi->load('student'),
            ]);
        }

        return redirect()->back()->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Remove the specified absensi from storage.
     */
    public function destroy(Request $request, Absensi $absensi): RedirectResponse|JsonResponse
    {
        $absensi->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Absensi deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Request $request, Absensi $absensi): RedirectResponse|JsonResponse
    {
        return $this->destroy($request, $absensi);
    }

    /**
     * Get the validation rules for the absensi request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'student_id' => ['required', 'exists:student_wali_kelas,id'],
            'hadir' => ['nullable', 'integer', 'min:0'],
            'izin' => ['nullable', 'integer', 'min:0'],
            'sakit' => ['nullable', 'integer', 'min:0'],
            'alpa' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
