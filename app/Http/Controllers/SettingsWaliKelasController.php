<?php

namespace App\Http\Controllers;

use App\Models\SettingsWaliKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsWaliKelasController extends Controller
{
    /**
     * Display a listing of the settings wali kelas.
     */
    public function index(Request $request): JsonResponse
    {
        $settings = SettingsWaliKelas::with('academicYear')->get();

        return response()->json($settings);
    }

    /**
     * Store a newly created settings wali kelas in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('school_logo_file')) {
            $path = $request->file('school_logo_file')->store('logos', 'public');
            $validated['school_logo'] = $path;
        }

        $settingsWaliKelas = SettingsWaliKelas::create($validated);

        return response()->json([
            'message' => 'Settings wali kelas created successfully.',
            'data' => $settingsWaliKelas->load('academicYear'),
        ], 201);
    }

    /**
     * Display the specified settings wali kelas.
     */
    public function show(SettingsWaliKelas $settingsWaliKelas): JsonResponse
    {
        return response()->json($settingsWaliKelas->load('academicYear'));
    }

    /**
     * Update the specified settings wali kelas in storage.
     */
    public function update(Request $request, SettingsWaliKelas $settingsWaliKelas): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('school_logo_file')) {
            $path = $request->file('school_logo_file')->store('logos', 'public');
            $validated['school_logo'] = $path;
        }

        $settingsWaliKelas->update($validated);

        return response()->json([
            'message' => 'Settings wali kelas updated successfully.',
            'data' => $settingsWaliKelas->load('academicYear'),
        ]);
    }

    /**
     * Remove the specified settings wali kelas from storage.
     */
    public function destroy(SettingsWaliKelas $settingsWaliKelas): JsonResponse
    {
        $settingsWaliKelas->delete();

        return response()->json([
            'message' => 'Settings wali kelas deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(SettingsWaliKelas $settingsWaliKelas): JsonResponse
    {
        return $this->destroy($settingsWaliKelas);
    }

    /**
     * Get the validation rules for the settings wali kelas request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'school_name' => ['required', 'string', 'max:255'],
            'npsn' => ['required', 'string', 'max:255'],
            'school_address' => ['required', 'string'],
            'principal_name' => ['required', 'string', 'max:255'],
            'school_logo' => ['nullable', 'string', 'max:255'],
            'school_logo_file' => ['nullable', 'image', 'max:2048'],
            'teacher_name' => ['required', 'string', 'max:255'],
            'teacher_nip' => ['nullable', 'string', 'max:255'],
            'teacher_email' => ['nullable', 'email', 'max:255'],
            'teacher_phone' => ['nullable', 'string', 'max:255'],
            'academicYear_id' => ['required', 'exists:academic_years,id'],
            'tanggal_penerimaan_rapor' => ['required', 'date'],
        ];
    }
}
