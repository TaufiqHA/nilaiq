<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\MapelSettings;
use App\Models\NilaiMapel;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NilaiMapelController extends Controller
{
    /**
     * Display a listing of the nilai mapels or render blade view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = NilaiMapel::with(['student', 'mapel']);

            if ($request->has('student_id')) {
                $query->where('student_id', $request->input('student_id'));
            }

            if ($request->has('mapel_id')) {
                $query->where('mapel_id', $request->input('mapel_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        $mapelSettings = collect();
        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->with('nilaiMapels')->get();
            $mapelSettings = MapelSettings::whereHas('settingsWaliKelas', function ($q) use ($classWaliKelas) {
                $q->where('class_id', $classWaliKelas->id);
            })->get();
        }

        return view('auth.waliKelas.nilaiMapel', compact('students', 'mapelSettings', 'classWaliKelas'));
    }

    /**
     * Store a newly created or updated nilai mapel in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $nilaiMapel = NilaiMapel::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'mapel_id' => $validated['mapel_id'],
            ],
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Nilai mapel created successfully.',
                'data' => $nilaiMapel->load(['student', 'mapel']),
            ], 201);
        }

        return redirect()->back()->with('success', 'Data nilai mapel berhasil disimpan.');
    }

    /**
     * Display the specified nilai mapel.
     */
    public function show(NilaiMapel $nilaiMapel): JsonResponse
    {
        return response()->json($nilaiMapel->load(['student', 'mapel']));
    }

    /**
     * Update the specified nilai mapel in storage.
     */
    public function update(Request $request, NilaiMapel $nilaiMapel): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $nilaiMapel->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Nilai mapel updated successfully.',
                'data' => $nilaiMapel->load(['student', 'mapel']),
            ]);
        }

        return redirect()->back()->with('success', 'Data nilai mapel berhasil diperbarui.');
    }

    /**
     * Remove the specified nilai mapel from storage.
     */
    public function destroy(Request $request, NilaiMapel $nilaiMapel): RedirectResponse|JsonResponse
    {
        $nilaiMapel->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Nilai mapel deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Data nilai mapel berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Request $request, NilaiMapel $nilaiMapel): RedirectResponse|JsonResponse
    {
        return $this->destroy($request, $nilaiMapel);
    }

    /**
     * Get the validation rules for the nilai mapel request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'student_id' => ['required', 'exists:student_wali_kelas,id'],
            'mapel_id' => ['required', 'exists:mapel_settings,id'],
            'nilai' => ['nullable', 'integer', 'min:0', 'max:100'],
            'capaian' => ['nullable', 'string'],
        ];
    }
}
