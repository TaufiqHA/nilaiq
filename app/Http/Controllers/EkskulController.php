<?php

namespace App\Http\Controllers;

use App\Models\Ekskul;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EkskulController extends Controller
{
    /**
     * Display a listing of the ekskuls.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ekskul::with('student');

        if ($request->has('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created ekskul in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $ekskul = Ekskul::create($validated);

        return response()->json([
            'message' => 'Ekskul created successfully.',
            'data' => $ekskul->load('student'),
        ], 201);
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
    public function update(Request $request, Ekskul $ekskul): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $ekskul->update($validated);

        return response()->json([
            'message' => 'Ekskul updated successfully.',
            'data' => $ekskul->load('student'),
        ]);
    }

    /**
     * Remove the specified ekskul from storage.
     */
    public function destroy(Ekskul $ekskul): JsonResponse
    {
        $ekskul->delete();

        return response()->json([
            'message' => 'Ekskul deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Ekskul $ekskul): JsonResponse
    {
        return $this->destroy($ekskul);
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
