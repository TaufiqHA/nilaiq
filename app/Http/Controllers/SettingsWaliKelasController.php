<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\MapelSettings;
use App\Models\SettingsWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsWaliKelasController extends Controller
{
    /**
     * Display a listing of the settings wali kelas.
     */
    public function index(Request $request): View|JsonResponse
    {
        $settingsWaliKelas = SettingsWaliKelas::with('academicYear')->first();
        $settings = SettingsWaliKelas::with('academicYear')->get();

        if ($request->wantsJson()) {
            return response()->json($settings);
        }

        return $this->renderMasterDataView($settingsWaliKelas);
    }

    /**
     * Store a newly created settings wali kelas in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse|View
    {
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('school_logo_file')) {
            $path = $request->file('school_logo_file')->store('logos', 'public');
            $validated['school_logo'] = $path;
        }

        $existing = SettingsWaliKelas::first();
        if ($existing) {
            $existing->update($validated);
            $settingsWaliKelas = $existing;
        } else {
            $settingsWaliKelas = SettingsWaliKelas::create($validated);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Settings wali kelas created successfully.',
                'data' => $settingsWaliKelas->load('academicYear'),
            ], 201);
        }

        return redirect()->route('settings-wali-kelas.index')->with('success', 'Data master wali kelas berhasil disimpan.');
    }

    /**
     * Display the specified settings wali kelas.
     */
    public function show(SettingsWaliKelas $settingsWaliKelas, Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json($settingsWaliKelas->load('academicYear'));
        }

        return $this->renderMasterDataView($settingsWaliKelas);
    }

    /**
     * Update the specified settings wali kelas in storage.
     */
    public function update(Request $request, SettingsWaliKelas $settingsWaliKelas): RedirectResponse|JsonResponse|View
    {
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('school_logo_file')) {
            $path = $request->file('school_logo_file')->store('logos', 'public');
            $validated['school_logo'] = $path;
        }

        $settingsWaliKelas->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Settings wali kelas updated successfully.',
                'data' => $settingsWaliKelas->load('academicYear'),
            ]);
        }

        return redirect()->route('settings-wali-kelas.index')->with('success', 'Data master wali kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified settings wali kelas from storage.
     */
    public function destroy(SettingsWaliKelas $settingsWaliKelas, Request $request): RedirectResponse|JsonResponse|View
    {
        $settingsWaliKelas->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Settings wali kelas deleted successfully.',
            ]);
        }

        return redirect()->route('settings-wali-kelas.index')->with('success', 'Data master wali kelas berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(SettingsWaliKelas $settingsWaliKelas, Request $request): RedirectResponse|JsonResponse|View
    {
        return $this->destroy($settingsWaliKelas, $request);
    }

    /**
     * Render the masterData view with necessary data.
     */
    private function renderMasterDataView(?SettingsWaliKelas $settingsWaliKelas = null): View
    {
        $settingsWaliKelas = $settingsWaliKelas ?? SettingsWaliKelas::with('academicYear')->first();

        $mapelSettings = $settingsWaliKelas
            ? MapelSettings::where('settingsWaliKelas_id', $settingsWaliKelas->id)->get()
            : MapelSettings::all();

        $academicYears = auth()->check()
            ? AcademicYear::where('user_id', auth()->id())->get()
            : AcademicYear::all();

        return view('auth.waliKelas.masterData', compact('settingsWaliKelas', 'mapelSettings', 'academicYears'));
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
