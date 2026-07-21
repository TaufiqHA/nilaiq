<?php

namespace App\Http\Controllers;

use App\Models\Recaps;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecapsController extends Controller
{
    /**
     * Display a listing of the recaps.
     */
    public function index(): JsonResponse
    {
        $recaps = Recaps::with(['academicYear', 'class', 'student'])->get();

        return response()->json($recaps);
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
