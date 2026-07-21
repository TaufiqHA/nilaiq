<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\Sikap;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SikapController extends Controller
{
    /**
     * Display a listing of the sikaps or render blade view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = Sikap::with('student');

            if ($request->has('student_id')) {
                $query->where('student_id', $request->input('student_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->with('sikap')->get();
        }

        return view('auth.waliKelas.sikap', compact('students', 'classWaliKelas'));
    }

    /**
     * Store a newly created or updated sikap in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $sikap = Sikap::updateOrCreate(
            ['student_id' => $validated['student_id']],
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sikap created successfully.',
                'data' => $sikap->load('student'),
            ], 201);
        }

        return redirect()->back()->with('success', 'Data sikap berhasil disimpan.');
    }

    /**
     * Display the specified sikap.
     */
    public function show(Sikap $sikap): JsonResponse
    {
        return response()->json($sikap->load('student'));
    }

    /**
     * Update the specified sikap in storage.
     */
    public function update(Request $request, Sikap $sikap): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $sikap->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sikap updated successfully.',
                'data' => $sikap->load('student'),
            ]);
        }

        return redirect()->back()->with('success', 'Data sikap berhasil diperbarui.');
    }

    /**
     * Remove the specified sikap from storage.
     */
    public function destroy(Request $request, Sikap $sikap): RedirectResponse|JsonResponse
    {
        $sikap->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Sikap deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Data sikap berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Request $request, Sikap $sikap): RedirectResponse|JsonResponse
    {
        return $this->destroy($request, $sikap);
    }

    /**
     * Get the validation rules for the sikap request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'student_id' => ['required', 'exists:student_wali_kelas,id'],
            'beriman_bertakwa_dan_berakhlak_mulia' => ['nullable', 'string'],
            'mandiri' => ['nullable', 'string'],
            'bergotong_royong' => ['nullable', 'string'],
            'kreatif' => ['nullable', 'string'],
            'bernalar_kritis' => ['nullable', 'string'],
            'berkebinekaan_global' => ['nullable', 'string'],
        ];
    }
}
