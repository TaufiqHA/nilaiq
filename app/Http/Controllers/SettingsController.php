<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Display a listing of the settings.
     */
    public function index(Request $request): JsonResponse|View
    {
        $setting = Settings::first() ?? new Settings;

        if ($request->wantsJson()) {
            return response()->json($setting);
        }

        return view('auth.MasterData', compact('setting'));
    }

    /**
     * Store a newly created settings in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('school_logo_file')) {
            $path = $request->file('school_logo_file')->store('logos', 'public');
            $validated['school_logo'] = $path;
        }

        $setting = Settings::first();
        if ($setting) {
            $setting->update($validated);
        } else {
            $setting = Settings::create($validated);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Settings saved successfully.',
                'data' => $setting,
            ], 200);
        }

        return redirect()->route('master-data.index')->with('success', 'Settings saved successfully.');
    }

    /**
     * Display the specified settings.
     */
    public function show(Request $request, Settings $setting): JsonResponse|View
    {
        if ($request->wantsJson()) {
            return response()->json($setting);
        }

        return view('auth.MasterData', compact('setting'));
    }

    /**
     * Update the specified settings in storage.
     */
    public function update(Request $request, Settings $setting): JsonResponse|RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        if ($request->hasFile('school_logo_file')) {
            $path = $request->file('school_logo_file')->store('logos', 'public');
            $validated['school_logo'] = $path;
        }

        $setting->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Settings updated successfully.',
                'data' => $setting,
            ]);
        }

        return redirect()->route('master-data.index')->with('success', 'Settings updated successfully.');
    }

    /**
     * Remove the specified settings from storage.
     */
    public function destroy(Settings $setting): JsonResponse|RedirectResponse
    {
        $setting->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Settings deleted successfully.']);
        }

        return redirect()->route('settings.index')->with('success', 'Settings deleted successfully.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Settings $setting): JsonResponse|RedirectResponse
    {
        return $this->destroy($setting);
    }

    /**
     * Get the validation rules for the settings request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'school_name' => ['nullable', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:255'],
            'school_address' => ['nullable', 'string'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'school_logo' => ['nullable', 'string', 'max:255'],
            'school_logo_file' => ['nullable', 'image', 'max:2048'],
            'teacher_name' => ['nullable', 'string', 'max:255'],
            'teacher_nip' => ['nullable', 'string', 'max:255'],
            'teacher_email' => ['nullable', 'email', 'max:255'],
            'teacher_phone' => ['nullable', 'string', 'max:255'],
            'subject_name' => ['nullable', 'string', 'max:255'],
            'minimum_score' => ['nullable', 'numeric', 'between:0,999.99'],
            'daily_test_weight' => ['nullable', 'numeric', 'between:0,999.99'],
            'assignment_weight' => ['nullable', 'numeric', 'between:0,999.99'],
            'midterm_weight' => ['nullable', 'numeric', 'between:0,999.99'],
            'final_weight' => ['nullable', 'numeric', 'between:0,999.99'],
        ];
    }
}
