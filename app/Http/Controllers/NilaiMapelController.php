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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Export Excel template containing students list and existing grades.
     */
    public function exportTemplate(Request $request): StreamedResponse|RedirectResponse
    {
        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();
        if (! $classWaliKelas) {
            return redirect()->back()->with('error', 'Anda belum mengatur informasi kelas.');
        }

        $mapelId = $request->input('mapel_id');
        if (! $mapelId) {
            return redirect()->back()->with('error', 'Mata pelajaran tidak dipilih.');
        }

        $mapel = MapelSettings::find($mapelId);
        if (! $mapel) {
            return redirect()->back()->with('error', 'Mata pelajaran tidak ditemukan.');
        }

        $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->get();
        if ($students->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada data siswa di kelas Anda.');
        }

        $nilaiMapelsKeyed = NilaiMapel::where('mapel_id', $mapelId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'ID Siswa (Jangan Diubah)');
        $sheet->setCellValue('B1', 'NIS');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'Nama Siswa');
        $sheet->setCellValue('E1', 'Nilai Akhir (0-100)');
        $sheet->setCellValue('F1', 'Capaian Pembelajaran');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE2E8F0');

        $row = 2;
        foreach ($students as $student) {
            $nilaiMapel = $nilaiMapelsKeyed->get($student->id);
            $nilaiValue = $nilaiMapel ? $nilaiMapel->nilai : '';
            $capaianValue = $nilaiMapel ? $nilaiMapel->capaian : '';

            $sheet->setCellValue('A'.$row, $student->id);
            $sheet->setCellValue('B'.$row, $student->nis);
            $sheet->setCellValue('C'.$row, $student->nisn);
            $sheet->setCellValue('D'.$row, $student->name);
            $sheet->setCellValue('E'.$row, $nilaiValue);
            $sheet->setCellValue('F'.$row, $capaianValue);

            $sheet->getStyle('E'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'Template_Nilai_'.str_replace(' ', '_', $mapel->mapel).'_'.str_replace(' ', '_', $classWaliKelas->name).'.xlsx';

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$fileName.'"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Import grades and learning achievements from Excel file.
     */
    public function import(Request $request): RedirectResponse
    {
        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();
        if (! $classWaliKelas) {
            return redirect()->back()->with('error', 'Anda belum mengatur informasi kelas.');
        }

        $validated = $request->validate([
            'mapel_id' => ['required', 'exists:mapel_settings,id'],
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $mapelId = $validated['mapel_id'];
        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $headerA = $sheet->getCell('A1')->getValue();
            $headerD = $sheet->getCell('D1')->getValue();
            if (strpos((string) $headerA, 'ID Siswa') === false || strpos((string) $headerD, 'Nama Siswa') === false) {
                return redirect()->back()->with('error', 'Format template Excel tidak valid. Pastikan menggunakan template yang diunduh dari sistem.');
            }

            $allowedStudentIds = StudentWaliKelas::where('class_id', $classWaliKelas->id)->pluck('id')->toArray();

            $updatedCount = 0;
            $deletedCount = 0;
            $errors = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $studentId = $sheet->getCell('A'.$row)->getValue();
                $studentName = $sheet->getCell('D'.$row)->getValue();
                $nilaiRaw = $sheet->getCell('E'.$row)->getValue();
                $capaianRaw = $sheet->getCell('F'.$row)->getValue();

                if (empty($studentId)) {
                    continue;
                }

                if (! in_array($studentId, $allowedStudentIds)) {
                    $errors[] = "Siswa di baris {$row} ({$studentName}) bukan anggota kelas Anda.";

                    continue;
                }

                $nilaiValue = ($nilaiRaw !== '' && ! is_null($nilaiRaw)) ? trim((string) $nilaiRaw) : null;
                $capaianValue = ($capaianRaw !== '' && ! is_null($capaianRaw)) ? trim((string) $capaianRaw) : null;

                if (! is_null($nilaiValue)) {
                    if (! is_numeric($nilaiValue) || $nilaiValue < 0 || $nilaiValue > 100) {
                        $errors[] = "Nilai untuk siswa {$studentName} (baris {$row}) harus berupa angka antara 0-100.";

                        continue;
                    }
                    $nilaiValue = (int) $nilaiValue;
                }

                if (is_null($nilaiValue) && is_null($capaianValue)) {
                    $deleted = NilaiMapel::where('student_id', $studentId)
                        ->where('mapel_id', $mapelId)
                        ->delete();
                    if ($deleted) {
                        $deletedCount++;
                    }

                    continue;
                }

                NilaiMapel::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'mapel_id' => $mapelId,
                    ],
                    [
                        'nilai' => $nilaiValue,
                        'capaian' => $capaianValue,
                    ]
                );

                $updatedCount++;
            }

            $successMsg = "Berhasil mengimpor nilai. {$updatedCount} data diperbarui/disimpan.";
            if ($deletedCount > 0) {
                $successMsg .= " {$deletedCount} data nilai dikosongkan.";
            }

            if (! empty($errors)) {
                return redirect()->back()
                    ->with('success', $successMsg)
                    ->withErrors($errors);
            }

            return redirect()->back()->with('success', $successMsg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file Excel: '.$e->getMessage());
        }
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
