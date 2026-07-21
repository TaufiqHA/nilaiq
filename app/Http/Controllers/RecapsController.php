<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\Recaps;
use App\Models\Settings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RecapsController extends Controller
{
    /**
     * Display a listing of the recaps in JSON.
     */
    public function index(): JsonResponse
    {
        $recaps = Recaps::with(['academicYear', 'class', 'student'])->get();

        return response()->json($recaps);
    }

    /**
     * Render the Nilai Akhir Blade view.
     */
    public function nilaiAkhirView(Request $request): View
    {
        $classes = Classes::with(['academicYear', 'students'])->get();
        $settings = Settings::first();

        $selectedClassId = $request->input('class_id', $classes->first()?->id);
        $selectedClass = null;
        $students = collect();
        $recapsKeyed = collect();

        if ($selectedClassId) {
            $selectedClass = Classes::with('academicYear')->find($selectedClassId);
            if ($selectedClass) {
                $students = $selectedClass->students()
                    ->where('status', 'ACTIVE')
                    ->orderBy('name', 'asc')
                    ->get();
                $recapsKeyed = Recaps::where('class_id', $selectedClassId)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('auth.nilaiAkhir', compact(
            'classes',
            'settings',
            'selectedClassId',
            'selectedClass',
            'students',
            'recapsKeyed'
        ));
    }

    /**
     * Store or update multiple recap records in batch.
     */
    public function batchStore(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'recaps' => ['required', 'array'],
            'recaps.*.student_id' => ['required', 'exists:students,id'],
            'recaps.*.final_score_result' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'recaps.*.competency_description' => ['nullable', 'string'],
        ]);

        $class = Classes::with('academicYear')->find($validated['class_id']);

        foreach ($validated['recaps'] as $item) {
            $scoreVal = (isset($item['final_score_result']) && $item['final_score_result'] !== '')
                ? floatval($item['final_score_result'])
                : null;
            $descVal = ! empty($item['competency_description']) ? $item['competency_description'] : null;

            Recaps::updateOrCreate(
                [
                    'class_id' => $class->id,
                    'student_id' => $item['student_id'],
                ],
                [
                    'academic_year_id' => $class->academic_year_id,
                    'final_score_result' => $scoreVal,
                    'competency_description' => $descVal,
                ]
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Data Nilai Akhir berhasil disimpan.',
            ]);
        }

        return redirect()->route('nilai-akhir.index', ['class_id' => $class->id])
            ->with('success', 'Data Nilai Akhir berhasil disimpan.');
    }

    /**
     * Store a newly created recap in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $recap = Recaps::create($validated);

        return response()->json([
            'message' => 'Recap created successfully.',
            'data' => $recap->load(['academicYear', 'class', 'student']),
        ], 201);
    }

    /**
     * Display the specified recap.
     */
    public function show(Recaps $recap): JsonResponse
    {
        return response()->json($recap->load(['academicYear', 'class', 'student']));
    }

    /**
     * Update the specified recap in storage.
     */
    public function update(Request $request, Recaps $recap): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $recap->update($validated);

        return response()->json([
            'message' => 'Recap updated successfully.',
            'data' => $recap->load(['academicYear', 'class', 'student']),
        ]);
    }

    /**
     * Remove the specified recap from storage.
     */
    public function destroy(Recaps $recap): JsonResponse
    {
        $recap->delete();

        return response()->json([
            'message' => 'Recap deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Recaps $recap): JsonResponse
    {
        return $this->destroy($recap);
    }

    /**
     * Get the validation rules for the recap request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'student_id' => ['required', 'exists:students,id'],
            'final_score_result' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'competency_description' => ['nullable', 'string'],
        ];
    }
}
