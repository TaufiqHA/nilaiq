<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\MapelSettings;
use App\Models\NilaiMapel;
use App\Models\SettingsWaliKelas;
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
        $selectedMapel = null;
        $nilaiMapelsKeyed = collect();

        $settingsWaliKelas = SettingsWaliKelas::whereHas('academicYear', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->first() ?? SettingsWaliKelas::first();

        if ($settingsWaliKelas) {
            $mapelSettings = MapelSettings::where('settingsWaliKelas_id', $settingsWaliKelas->id)->get();
        }

        if ($mapelSettings->isEmpty()) {
            $mapelSettings = MapelSettings::all();
        }

        $selectedMapelId = $request->input('mapel_id', $mapelSettings->first()?->id);
        if ($selectedMapelId) {
            $selectedMapel = $mapelSettings->firstWhere('id', $selectedMapelId) ?? MapelSettings::find($selectedMapelId);
        }

        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->get();
        } else {
            $students = StudentWaliKelas::all();
        }

        if ($selectedMapelId) {
            $nilaiMapelsKeyed = NilaiMapel::where('mapel_id', $selectedMapelId)
                ->when($classWaliKelas, function ($q) use ($classWaliKelas) {
                    $q->whereHas('student', function ($sub) use ($classWaliKelas) {
                        $sub->where('class_id', $classWaliKelas->id);
                    });
                })
                ->get()
                ->keyBy('student_id');
        }

        return view('auth.waliKelas.nilai', compact('students', 'mapelSettings', 'selectedMapel', 'nilaiMapelsKeyed', 'classWaliKelas'));
    }

    /**
     * Store or update multiple nilai mapels in batch for a specific mapel.
     */
    public function batchStore(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'mapel_id' => ['required', 'exists:mapel_settings,id'],
            'scores' => ['required', 'array'],
            'scores.*.student_id' => ['required', 'exists:student_wali_kelas,id'],
            'scores.*.nilai' => ['nullable', 'integer', 'min:0', 'max:100'],
            'scores.*.capaian' => ['nullable', 'string'],
        ]);

        $mapelId = $validated['mapel_id'];
        $createdOrUpdated = [];

        foreach ($validated['scores'] as $scoreData) {
            $nilaiValue = (isset($scoreData['nilai']) && $scoreData['nilai'] !== '') ? (int) $scoreData['nilai'] : null;
            $capaianValue = ! empty($scoreData['capaian']) ? $scoreData['capaian'] : null;

            if (is_null($nilaiValue) && is_null($capaianValue)) {
                NilaiMapel::where('student_id', $scoreData['student_id'])
                    ->where('mapel_id', $mapelId)
                    ->delete();

                continue;
            }

            $nilaiMapel = NilaiMapel::updateOrCreate(
                [
                    'student_id' => $scoreData['student_id'],
                    'mapel_id' => $mapelId,
                ],
                [
                    'nilai' => $nilaiValue,
                    'capaian' => $capaianValue,
                ]
            );

            $createdOrUpdated[] = $nilaiMapel;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Data nilai mapel berhasil disimpan secara kolektif.',
                'data' => $createdOrUpdated,
            ]);
        }

        return redirect()->route('wali-kelas.nilai-mapel', ['mapel_id' => $mapelId])
            ->with('success', 'Data nilai mata pelajaran berhasil disimpan.');
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

        return redirect()->route('wali-kelas.nilai-mapel', ['mapel_id' => $validated['mapel_id']])
            ->with('success', 'Data nilai mapel berhasil disimpan.');
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

        return redirect()->route('wali-kelas.nilai-mapel', ['mapel_id' => $validated['mapel_id']])
            ->with('success', 'Data nilai mapel berhasil diperbarui.');
    }

    /**
     * Remove the specified nilai mapel from storage.
     */
    public function destroy(Request $request, NilaiMapel $nilaiMapel): RedirectResponse|JsonResponse
    {
        $mapelId = $nilaiMapel->mapel_id;
        $nilaiMapel->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Nilai mapel deleted successfully.',
            ]);
        }

        return redirect()->route('wali-kelas.nilai-mapel', ['mapel_id' => $mapelId])
            ->with('success', 'Data nilai mapel berhasil dihapus.');
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
