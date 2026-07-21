<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\MapelSettings;
use App\Models\SettingsWaliKelas;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RaportController extends Controller
{
    /**
     * Display a listing of students for Raport Wali Kelas.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $search = $request->input('search');

        $students = collect();
        if ($classWaliKelas) {
            $query = StudentWaliKelas::where('class_id', $classWaliKelas->id)
                ->with(['sikap', 'absensi', 'catatanWaliKelas', 'nilaiMapels']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            }

            $students = $query->orderBy('name', 'asc')->get();
        }

        $settingsWaliKelas = SettingsWaliKelas::whereHas('academicYear', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->first() ?? SettingsWaliKelas::first();

        $mapelSettings = collect();
        if ($settingsWaliKelas) {
            $mapelSettings = MapelSettings::where('settingsWaliKelas_id', $settingsWaliKelas->id)->get();
        }
        if ($mapelSettings->isEmpty()) {
            $mapelSettings = MapelSettings::all();
        }

        return view('auth.waliKelas.raport', compact('students', 'classWaliKelas', 'search', 'mapelSettings'));
    }

    /**
     * Update subject groups (Kelompok A / B).
     */
    public function updateKelompok(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mapels' => ['required', 'array'],
            'mapels.*.id' => ['required', 'exists:mapel_settings,id'],
            'mapels.*.kelompok' => ['required', 'in:A,B'],
        ]);

        foreach ($validated['mapels'] as $mapelData) {
            MapelSettings::where('id', $mapelData['id'])->update([
                'kelompok' => $mapelData['kelompok'],
            ]);
        }

        return redirect()->route('wali-kelas.raport')
            ->with('success', 'Pengaturan kelompok mata pelajaran berhasil diperbarui.');
    }

    /**
     * Print single student report card.
     */
    public function cetak(StudentWaliKelas $student): View
    {
        $data = $this->prepareStudentRaportData($student);

        return view('auth.waliKelas.raportCetak', [
            'reports' => [$data],
            'isMultiple' => false,
        ]);
    }

    /**
     * Print all students report cards in the class.
     */
    public function cetakSemua(): View
    {
        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        if (! $classWaliKelas) {
            abort(404, 'Data Kelas Wali Kelas tidak ditemukan.');
        }

        $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)
            ->orderBy('name', 'asc')
            ->get();

        $reports = [];
        foreach ($students as $student) {
            $reports[] = $this->prepareStudentRaportData($student);
        }

        return view('auth.waliKelas.raportCetak', [
            'reports' => $reports,
            'isMultiple' => true,
        ]);
    }

    /**
     * Helper to assemble full report card data structure for a single student.
     *
     * @return array<string, mixed>
     */
    private function prepareStudentRaportData(StudentWaliKelas $student): array
    {
        $student->load([
            'classWaliKelas',
            'sikap',
            'absensi',
            'ekskul',
            'prestasi',
            'catatanWaliKelas',
            'nilaiMapels.mapel',
        ]);

        $userId = auth()->id();

        $settingsWaliKelas = SettingsWaliKelas::with('academicYear')
            ->whereHas('academicYear', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->first() ?? SettingsWaliKelas::with('academicYear')->first();

        $allMapels = collect();
        if ($settingsWaliKelas) {
            $allMapels = MapelSettings::where('settingsWaliKelas_id', $settingsWaliKelas->id)->get();
        }

        if ($allMapels->isEmpty()) {
            $allMapels = MapelSettings::all();
        }

        // Group subjects into Kelompok A and Kelompok B
        $kelompokA = collect();
        $kelompokB = collect();

        foreach ($allMapels as $mapel) {
            if (($mapel->kelompok ?? 'A') === 'B') {
                $kelompokB->push($mapel);
            } else {
                $kelompokA->push($mapel);
            }
        }

        // Map Nilai for each subject
        $nilaiKeyed = $student->nilaiMapels->keyBy('mapel_id');

        // Calculate Ranking & Total Scores for the whole class
        $classId = $student->class_id;
        $classStudents = StudentWaliKelas::where('class_id', $classId)
            ->with('nilaiMapels')
            ->get();

        $studentRankings = [];
        foreach ($classStudents as $cStudent) {
            $sum = $cStudent->nilaiMapels->sum('nilai');
            $studentRankings[] = [
                'student_id' => $cStudent->id,
                'total_score' => $sum,
                'name' => $cStudent->name,
            ];
        }

        usort($studentRankings, function ($a, $b) {
            if ($b['total_score'] === $a['total_score']) {
                return strcmp($a['name'], $b['name']);
            }

            return $b['total_score'] <=> $a['total_score'];
        });

        $peringkat = 1;
        $prevScore = null;
        $studentRank = 1;
        $studentTotalScore = 0;

        foreach ($studentRankings as $idx => $rData) {
            if ($prevScore !== null && $rData['total_score'] < $prevScore) {
                $peringkat = $idx + 1;
            }
            if ($rData['student_id'] === $student->id) {
                $studentRank = $peringkat;
                $studentTotalScore = $rData['total_score'];
            }
            $prevScore = $rData['total_score'];
        }

        return [
            'student' => $student,
            'classWaliKelas' => $student->classWaliKelas,
            'settingsWaliKelas' => $settingsWaliKelas,
            'kelompokA' => $kelompokA,
            'kelompokB' => $kelompokB,
            'nilaiKeyed' => $nilaiKeyed,
            'jumlahNilai' => $studentTotalScore,
            'peringkat' => $studentRank,
            'totalSiswa' => count($studentRankings),
        ];
    }
}
