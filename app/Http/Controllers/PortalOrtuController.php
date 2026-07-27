<?php

namespace App\Http\Controllers;

use App\Models\Attendances;
use App\Models\Settings;
use App\Models\Students;
use App\Models\StudentWaliKelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalOrtuController extends Controller
{
    /**
     * Show the parent portal search page.
     */
    public function index(): View|RedirectResponse
    {
        if (session()->has('portal_student_id')) {
            return redirect()->route('portal-ortu.dashboard');
        }

        return view('portal.index');
    }

    /**
     * Process student search request.
     */
    public function search(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'nis' => ['required', 'string'],
            'nisn' => ['required', 'string'],
        ]);

        $nameInput = trim(preg_replace('/\s+/', ' ', $request->input('name')));
        $nisInput = trim($request->input('nis'));
        $nisnInput = trim($request->input('nisn'));

        // 1. Search in StudentWaliKelas
        $student = StudentWaliKelas::where('nis', $nisInput)
            ->where('nisn', $nisnInput)
            ->first();

        if ($student) {
            $studentNameNormalized = trim(preg_replace('/\s+/', ' ', $student->name));
            if (strcasecmp($studentNameNormalized, $nameInput) === 0) {
                session([
                    'portal_student_type' => 'wali_kelas',
                    'portal_student_id' => $student->id,
                ]);

                return redirect()->route('portal-ortu.dashboard')
                    ->with('success', 'Berhasil masuk ke Portal Orang Tua.');
            }
        }

        // 2. Search in Students
        $student = Students::where('nis', $nisInput)
            ->where('nisn', $nisnInput)
            ->first();

        if ($student) {
            $studentNameNormalized = trim(preg_replace('/\s+/', ' ', $student->name));
            if (strcasecmp($studentNameNormalized, $nameInput) === 0) {
                session([
                    'portal_student_type' => 'mapel',
                    'portal_student_id' => $student->id,
                ]);

                return redirect()->route('portal-ortu.dashboard')
                    ->with('success', 'Berhasil masuk ke Portal Orang Tua.');
            }
        }

        return redirect()->back()
            ->withErrors(['search_error' => 'Data siswa tidak ditemukan. Silakan periksa kembali Nama, NIS, dan NISN siswa.'])
            ->withInput();
    }

    /**
     * Show the parent portal dashboard.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! session()->has('portal_student_id')) {
            return redirect()->route('portal-ortu.index');
        }

        $studentId = session('portal_student_id');
        $studentType = session('portal_student_type', 'wali_kelas');

        if ($studentType === 'wali_kelas') {
            $student = StudentWaliKelas::with([
                'classWaliKelas.academicYear',
                'absensi',
                'ekskul',
                'prestasi',
                'sikap',
                'catatanWaliKelas',
                'nilaiMapels.mapel',
            ])->findOrFail($studentId);

            // Group subjects into Kelompok A and Kelompok B
            $kelompokA = collect();
            $kelompokB = collect();

            foreach ($student->nilaiMapels as $nilaiMapel) {
                $mapel = $nilaiMapel->mapel;
                if ($mapel) {
                    if (($mapel->kelompok ?? 'A') === 'B') {
                        $kelompokB->push($nilaiMapel);
                    } else {
                        $kelompokA->push($nilaiMapel);
                    }
                }
            }

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

            $totalSiswa = count($studentRankings);

            return view('portal.dashboard', compact(
                'student',
                'studentType',
                'kelompokA',
                'kelompokB',
                'studentRank',
                'studentTotalScore',
                'totalSiswa'
            ));
        } else {
            // For Guru Mapel student type ('mapel')
            $student = Students::with([
                'class.academicYear',
                'dailyTestScores.dailyTestMeeting',
                'assignmentScores.assignmentMeeting',
                'midtermScores.midtermExam',
                'finalScores.finalExam',
            ])->findOrFail($studentId);

            // Fetch subject teacher settings to get KKM and Subject Name
            $settings = Settings::first();
            $subjectName = $settings?->subject_name ?? 'Mata Pelajaran';
            $kkm = $settings?->minimum_score ?? 75;

            // Compile evaluation grades
            $evaluations = collect();

            foreach ($student->dailyTestScores as $score) {
                $evaluations->push((object) [
                    'type' => 'Ulangan Harian',
                    'title' => $score->dailyTestMeeting?->title ?? 'Ulangan Harian',
                    'date' => $score->dailyTestMeeting?->test_date,
                    'score' => $score->score,
                ]);
            }

            foreach ($student->assignmentScores as $score) {
                $evaluations->push((object) [
                    'type' => 'Tugas',
                    'title' => $score->assignmentMeeting?->title ?? 'Tugas',
                    'date' => $score->assignmentMeeting?->assignment_date,
                    'score' => $score->score,
                ]);
            }

            foreach ($student->midtermScores as $score) {
                $evaluations->push((object) [
                    'type' => 'UTS',
                    'title' => $score->midtermExam?->title ?? 'UTS',
                    'date' => $score->midtermExam?->exam_date,
                    'score' => $score->score,
                ]);
            }

            foreach ($student->finalScores as $score) {
                $evaluations->push((object) [
                    'type' => 'UAS',
                    'title' => $score->finalExam?->title ?? 'UAS',
                    'date' => $score->finalExam?->exam_date,
                    'score' => $score->score,
                ]);
            }

            // Sort evaluations by date (if available) or type
            $evaluations = $evaluations->sortBy('date')->values();

            // Calculate attendance stats dynamically
            $attendances = Attendances::where('student_id', $student->id)->get();
            $hadir = $attendances->where('status', 'HADIR')->count();
            $sakit = $attendances->where('status', 'SAKIT')->count();
            $izin = $attendances->where('status', 'IZIN')->count();
            $alpa = $attendances->whereIn('status', ['ALPA', 'BOLOS', 'TANPA_KETERANGAN'])->count();

            // We represent absensi as an object to match dashboard layout
            $absensi = (object) [
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpa' => $alpa,
            ];

            return view('portal.dashboard', compact(
                'student',
                'studentType',
                'subjectName',
                'kkm',
                'evaluations',
                'absensi'
            ));
        }
    }

    /**
     * Exit the parent portal.
     */
    public function exit(): RedirectResponse
    {
        session()->forget(['portal_student_id', 'portal_student_type']);

        return redirect()->route('portal-ortu.index')
            ->with('success', 'Anda telah keluar dari Portal Orang Tua.');
    }
}
