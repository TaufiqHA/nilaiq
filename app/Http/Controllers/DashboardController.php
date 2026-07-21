<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AcademicYear;
use App\Models\AssignmentMeetings;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\ClassWaliKelas;
use App\Models\DailyTestMeetings;
use App\Models\FinalExams;
use App\Models\MidtermExams;
use App\Models\Settings;
use App\Models\SettingsWaliKelas;
use App\Models\Students;
use App\Models\StudentWaliKelas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): View|JsonResponse
    {
        // 1. Total Siswa Aktif
        $totalStudents = Students::where('status', 'ACTIVE')->count();

        // 2. Total Kelas
        $totalClasses = Classes::count();

        // 3. Tahun Ajaran Aktif
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        // 4. Rata-rata Kehadiran
        $totalAttendances = Attendances::count();
        $presentAttendances = Attendances::where('status', 'HADIR')->count();
        $attendanceRate = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 2)
            : 0;

        // 5. Settings (Mata Pelajaran & KKM)
        $settings = Settings::first();

        // 6. Jumlah Kegiatan Evaluasi
        $dailyTestCount = DailyTestMeetings::count();
        $assignmentCount = AssignmentMeetings::count();
        $midtermCount = MidtermExams::count();
        $finalCount = FinalExams::count();

        // 7. Siswa Terbaru (5 siswa terakhir)
        $latestStudents = Students::with('class')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // 8. Kegiatan Terbaru (Kombinasi 5 pertemuan/ujian terakhir)
        $recentActivities = collect();

        $dailyTests = DailyTestMeetings::with('class')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'type' => 'Ulangan Harian',
                'title' => $item->title,
                'date' => $item->test_date,
                'class' => $item->class?->name ?? 'Semua',
                'created_at' => $item->created_at,
            ]);

        $assignments = AssignmentMeetings::with('class')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'type' => 'Tugas',
                'title' => $item->title,
                'date' => $item->assignment_date,
                'class' => $item->class?->name ?? 'Semua',
                'created_at' => $item->created_at,
            ]);

        $midterms = MidtermExams::with('class')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'type' => 'UTS',
                'title' => $item->title,
                'date' => $item->exam_date,
                'class' => $item->class?->name ?? 'Semua',
                'created_at' => $item->created_at,
            ]);

        $finals = FinalExams::with('class')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'type' => 'UAS',
                'title' => $item->title,
                'date' => $item->exam_date,
                'class' => $item->class?->name ?? 'Semua',
                'created_at' => $item->created_at,
            ]);

        $recentActivities = $recentActivities
            ->concat($dailyTests)
            ->concat($assignments)
            ->concat($midterms)
            ->concat($finals)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $data = compact(
            'totalStudents',
            'totalClasses',
            'activeAcademicYear',
            'attendanceRate',
            'settings',
            'dailyTestCount',
            'assignmentCount',
            'midtermCount',
            'finalCount',
            'latestStudents',
            'recentActivities'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('auth.dashboard', $data);
    }

    /**
     * Display the Wali Kelas dashboard.
     */
    public function waliKelas(Request $request): View|JsonResponse
    {
        $userId = auth()->id();
        $classWaliKelas = ClassWaliKelas::where('user_id', $userId)->first();

        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        $settingsWaliKelas = SettingsWaliKelas::first();

        $students = collect();
        $totalStudents = 0;
        $activeStudentsCount = 0;
        $maleStudentsCount = 0;
        $femaleStudentsCount = 0;
        $attendanceRate = 0;

        if ($classWaliKelas) {
            $students = StudentWaliKelas::where('class_id', $classWaliKelas->id)->get();
            $totalStudents = $students->count();
            $activeStudentsCount = $students->where('status', 'ACTIVE')->count();
            $maleStudentsCount = $students->where('gender', 'L')->count();
            $femaleStudentsCount = $students->where('gender', 'P')->count();

            $studentIds = $students->pluck('id');
            $absensiRecords = Absensi::whereIn('student_id', $studentIds)->get();
            $totalHadir = $absensiRecords->sum('hadir');
            $totalIzin = $absensiRecords->sum('izin');
            $totalSakit = $absensiRecords->sum('sakit');
            $totalAlpa = $absensiRecords->sum('alpa');
            $totalAbsensi = $totalHadir + $totalIzin + $totalSakit + $totalAlpa;

            if ($totalAbsensi > 0) {
                $attendanceRate = round(($totalHadir / $totalAbsensi) * 100, 2);
            }
        }

        $data = compact(
            'activeAcademicYear',
            'settingsWaliKelas',
            'classWaliKelas',
            'students',
            'totalStudents',
            'activeStudentsCount',
            'maleStudentsCount',
            'femaleStudentsCount',
            'attendanceRate'
        );

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('auth.waliKelas.dashboard', $data);
    }
}
