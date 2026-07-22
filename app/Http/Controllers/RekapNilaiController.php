<?php

namespace App\Http\Controllers;

use App\Models\ClassWaliKelas;
use App\Models\MapelSettings;
use App\Models\SettingsWaliKelas;
use App\Models\StudentWaliKelas;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class RekapNilaiController extends Controller
{
    /**
     * Display the grade recap page for Wali Kelas.
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $students = collect();
        $mapelSettings = collect();
        $ranks = [];
        $studentScoresData = [];
        $classAverage = 0;
        $highestAverage = 0;
        $lowestAverage = 0;

        $settingsWaliKelas = SettingsWaliKelas::whereHas('academicYear', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->first() ?? SettingsWaliKelas::first();

        if ($settingsWaliKelas) {
            $mapelSettings = MapelSettings::where('settingsWaliKelas_id', $settingsWaliKelas->id)->get();
        }

        if ($mapelSettings->isEmpty()) {
            $mapelSettings = MapelSettings::all();
        }

        if ($classWaliKelas) {
            $search = $request->input('search');

            $query = StudentWaliKelas::where('class_id', $classWaliKelas->id)
                ->with(['nilaiMapels']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            }

            $students = $query->orderBy('name', 'asc')->get();

            $allClassStudents = StudentWaliKelas::where('class_id', $classWaliKelas->id)
                ->with(['nilaiMapels'])
                ->get();

            $studentRankings = [];
            foreach ($allClassStudents as $student) {
                $sum = 0;
                $count = 0;
                foreach ($mapelSettings as $mapel) {
                    $nilaiObj = $student->nilaiMapels->firstWhere('mapel_id', $mapel->id);
                    if ($nilaiObj && ! is_null($nilaiObj->nilai)) {
                        $sum += $nilaiObj->nilai;
                        $count++;
                    }
                }
                $avg = $count > 0 ? ($sum / $count) : 0;
                $studentRankings[] = [
                    'student_id' => $student->id,
                    'average_score' => $avg,
                    'name' => $student->name,
                ];
            }

            usort($studentRankings, function ($a, $b) {
                if ($b['average_score'] == $a['average_score']) {
                    return strcmp($a['name'], $b['name']);
                }

                return $b['average_score'] <=> $a['average_score'];
            });

            $peringkat = 1;
            $prevScore = null;
            foreach ($studentRankings as $idx => $rData) {
                if ($prevScore !== null && $rData['average_score'] < $prevScore) {
                    $peringkat = $idx + 1;
                }
                $ranks[$rData['student_id']] = $peringkat;
                $prevScore = $rData['average_score'];
            }

            foreach ($students as $student) {
                $sum = 0;
                $count = 0;
                $studentScores = [];

                foreach ($mapelSettings as $mapel) {
                    $nilaiObj = $student->nilaiMapels->firstWhere('mapel_id', $mapel->id);
                    $val = ($nilaiObj && ! is_null($nilaiObj->nilai)) ? $nilaiObj->nilai : null;
                    $studentScores[$mapel->id] = $val;
                    if (! is_null($val)) {
                        $sum += $val;
                        $count++;
                    }
                }

                $studentScoresData[$student->id] = [
                    'scores' => $studentScores,
                    'jumlah' => $sum,
                    'rata_rata' => $count > 0 ? ($sum / $count) : 0,
                    'count' => $count,
                ];
            }

            if (count($studentRankings) > 0) {
                $sumAverages = 0;
                $averages = [];
                foreach ($studentRankings as $rData) {
                    $sumAverages += $rData['average_score'];
                    $averages[] = $rData['average_score'];
                }
                $classAverage = $sumAverages / count($studentRankings);
                $highestAverage = max($averages);
                $lowestAverage = min($averages);
            }
        }

        return view('auth.waliKelas.rekapNilai', compact(
            'classWaliKelas',
            'students',
            'mapelSettings',
            'ranks',
            'studentScoresData',
            'classAverage',
            'highestAverage',
            'lowestAverage',
            'settingsWaliKelas'
        ));
    }
}
