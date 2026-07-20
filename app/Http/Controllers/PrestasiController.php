<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\Prestasi;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PrestasiController extends Controller
{
    /**
     * Display a listing of the prestasis or render blade view.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->wantsJson()) {
            $query = Prestasi::with('student');

            if ($request->has('student_id')) {
                $query->where('student_id', $request->input('student_id'));
            }

            return response()->json($query->get());
        }

        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->with('prestasi')->get();
        }

        return view('auth.waliKelas.prestasi', compact('students', 'classWaliKelas'));
    }

    /**
     * Store a newly created or updated prestasi in storage.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $prestasi = Prestasi::updateOrCreate(
            ['student_id' => $validated['student_id']],
            $validated
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Prestasi created successfully.',
                'data' => $prestasi->load('student'),
            ], 201);
        }

        return redirect()->back()->with('success', 'Data prestasi berhasil disimpan.');
    }

    /**
     * Display the specified prestasi.
     */
    public function show(Prestasi $prestasi): JsonResponse
    {
        return response()->json($prestasi->load('student'));
    }

    /**
     * Update the specified prestasi in storage.
     */
    public function update(Request $request, Prestasi $prestasi): RedirectResponse|JsonResponse
    {
        $validated = $request->validate($this->validationRules());

        $prestasi->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Prestasi updated successfully.',
                'data' => $prestasi->load('student'),
            ]);
        }

        return redirect()->back()->with('success', 'Data prestasi berhasil diperbarui.');
    }

    /**
     * Remove the specified prestasi from storage.
     */
    public function destroy(Request $request, Prestasi $prestasi): RedirectResponse|JsonResponse
    {
        $prestasi->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Prestasi deleted successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Data prestasi berhasil dihapus.');
    }

    /**
     * Alias for destroy method to support explicit 'delete' request.
     */
    public function delete(Request $request, Prestasi $prestasi): RedirectResponse|JsonResponse
    {
        return $this->destroy($request, $prestasi);
    }

    /**
     * Get the validation rules for the prestasi request.
     *
     * @return array<string, array<int, string>>
     */
    private function validationRules(): array
    {
        return [
            'student_id' => ['required', 'exists:student_wali_kelas,id'],
            'prestasi1' => ['nullable', 'integer'],
            'catatan_prestasi1' => ['nullable', 'string', 'max:255'],
            'prestasi2' => ['nullable', 'integer'],
            'catatan_prestasi2' => ['nullable', 'string', 'max:255'],
            'prestasi3' => ['nullable', 'integer'],
            'catatan_prestasi3' => ['nullable', 'string', 'max:255'],
        ];
    }
}
