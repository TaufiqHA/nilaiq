<?php

namespace App\Http\Controllers;

use App\Models\MapelSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MapelSettingsController extends Controller
{
    /**
     * Display a listing of the mapel settings.
     */
    public function index(Request $request): JsonResponse
    {
        $query = MapelSettings::with('settingsWaliKelas');

        if ($request->has('settingsWaliKelas_id')) {
            $query->where('settingsWaliKelas_id', $request->input('settingsWaliKelas_id'));
        }

        return response()->json($query->get());
    }

    /**
     * Store a newly created mapel settings in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $mapelSetting = MapelSettings::create($validated);

        return response()->json([
            'message' => 'Mapel setting created successfully.',
            'data' => $mapelSetting->load('settingsWaliKelas'),
        ], 201);
    }

    /**
     * Display the specified mapel settings.
     */
    public function show(MapelSettings $mapelSetting): JsonResponse
    {
        return response()->json($mapelSetting->load('settingsWaliKelas'));
    }

    /**
     * Update the specified mapel settings in storage.
     */
    public function update(Request $request, MapelSettings $mapelSetting): JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $mapelSetting->update($validated);

        return response()->json([
            'message' => 'Mapel setting updated successfully.',
            'data' => $mapelSetting->load('settingsWaliKelas'),
        ]);
    }

    /**
     * Remove the specified mapel settings from storage.
     */
    public function destroy(MapelSettings $mapelSetting): JsonResponse
    {
        $mapelSetting->delete();

        return response()->json([
            'message' => 'Mapel setting deleted successfully.',
        ]);
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(MapelSettings $mapelSetting): JsonResponse
    {
        return $this->destroy($mapelSetting);
    }

    /**
     * Get the validation rules for the mapel settings request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'settingsWaliKelas_id' => ['required', 'exists:settings_wali_kelas,id'],
            'mapel' => ['required', 'string', 'max:255'],
            'guru' => ['required', 'string', 'max:255'],
            'kkm' => ['required', 'integer', 'min:0', 'max:100'],
        ];
    }
}
