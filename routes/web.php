<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AssignmentMeetingsController;
use App\Http\Controllers\AssignmentScoresController;
use App\Http\Controllers\AttendanceMeetingsController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ClassWaliKelasController;
use App\Http\Controllers\DailyTestMeetingsController;
use App\Http\Controllers\DailyTestScoresController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinalExamsController;
use App\Http\Controllers\FinalScoresController;
use App\Http\Controllers\MapelSettingsController;
use App\Http\Controllers\MidtermExamsController;
use App\Http\Controllers\MidtermScoresController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SettingsWaliKelasController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\StudentWaliKelasController;
use App\Http\Middleware\WaliKelasMiddleware;
use App\Models\AcademicYear;
use App\Models\Attendances;
use App\Models\SettingsWaliKelas;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/master-data', [SettingsController::class, 'index'])->name('master-data.index');
    Route::post('/master-data', [SettingsController::class, 'store'])->name('master-data.store');

    Route::resource('settings', SettingsController::class);
    Route::delete('settings/{setting}/delete', [SettingsController::class, 'delete'])->name('settings.delete');

    Route::resource('academic-years', AcademicYearController::class);
    Route::delete('academic-years/{academic_year}/delete', [AcademicYearController::class, 'delete'])->name('academic-years.delete');

    Route::resource('classes', ClassesController::class);
    Route::delete('classes/{class}/delete', [ClassesController::class, 'delete'])->name('classes.delete');

    Route::post('students/import', [StudentsController::class, 'import'])->name('students.import');
    Route::resource('students', StudentsController::class);
    Route::delete('students/{student}/delete', [StudentsController::class, 'delete'])->name('students.delete');

    Route::resource('attendance-meetings', AttendanceMeetingsController::class);
    Route::delete('attendance-meetings/{attendance_meeting}/delete', [AttendanceMeetingsController::class, 'delete'])->name('attendance-meetings.delete');

    Route::resource('attendances', AttendancesController::class);
    Route::delete('attendances/{attendance}/delete', [AttendancesController::class, 'delete'])->name('attendances.delete');

    Route::resource('daily-test-meetings', DailyTestMeetingsController::class);
    Route::delete('daily-test-meetings/{daily_test_meeting}/delete', [DailyTestMeetingsController::class, 'delete'])->name('daily-test-meetings.delete');

    Route::resource('daily-test-scores', DailyTestScoresController::class);
    Route::delete('daily-test-scores/{daily_test_score}/delete', [DailyTestScoresController::class, 'delete'])->name('daily-test-scores.delete');

    Route::resource('assignment-meetings', AssignmentMeetingsController::class);
    Route::delete('assignment-meetings/{assignment_meeting}/delete', [AssignmentMeetingsController::class, 'delete'])->name('assignment-meetings.delete');

    Route::resource('assignment-scores', AssignmentScoresController::class);
    Route::delete('assignment-scores/{assignment_score}/delete', [AssignmentScoresController::class, 'delete'])->name('assignment-scores.delete');

    Route::resource('midterm-exams', MidtermExamsController::class);
    Route::delete('midterm-exams/{midterm_exam}/delete', [MidtermExamsController::class, 'delete'])->name('midterm-exams.delete');

    Route::resource('final-exams', FinalExamsController::class);
    Route::delete('final-exams/{final_exam}/delete', [FinalExamsController::class, 'delete'])->name('final-exams.delete');

    Route::resource('midterm-scores', MidtermScoresController::class);
    Route::delete('midterm-scores/{midterm_score}/delete', [MidtermScoresController::class, 'delete'])->name('midterm-scores.delete');

    Route::resource('final-scores', FinalScoresController::class);
    Route::delete('final-scores/{final_score}/delete', [FinalScoresController::class, 'delete'])->name('final-scores.delete');

    Route::resource('settings-wali-kelas', SettingsWaliKelasController::class)->parameters(['settings-wali-kelas' => 'settings_wali_kelas']);
    Route::delete('settings-wali-kelas/{settings_wali_kelas}/delete', [SettingsWaliKelasController::class, 'delete'])->name('settings-wali-kelas.delete');

    // Routes khusus Wali Kelas
    Route::middleware([WaliKelasMiddleware::class])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
        Route::get('/dashboard', function (Request $request) {
            $activeAcademicYear = AcademicYear::where('is_active', true)->first();
            $settingsWaliKelas = SettingsWaliKelas::first();
            $totalStudents = Students::where('status', 'ACTIVE')->count();

            $totalAttendances = Attendances::count();
            $presentAttendances = Attendances::where('status', 'HADIR')->count();
            $attendanceRate = $totalAttendances > 0
                ? round(($presentAttendances / $totalAttendances) * 100, 2)
                : 0;

            $data = compact('activeAcademicYear', 'settingsWaliKelas', 'totalStudents', 'attendanceRate');

            if ($request->wantsJson()) {
                return response()->json($data);
            }

            return view('auth.waliKelas.dashboard', $data);
        })->name('dashboard');

        Route::resource('mapel-settings', MapelSettingsController::class)->parameters(['mapel-settings' => 'mapel_setting']);
        Route::delete('mapel-settings/{mapel_setting}/delete', [MapelSettingsController::class, 'delete'])->name('mapel-settings.delete');

        Route::resource('class-wali-kelas', ClassWaliKelasController::class)->parameters(['class-wali-kelas' => 'class_wali_kelas']);
        Route::delete('class-wali-kelas/{class_wali_kelas}/delete', [ClassWaliKelasController::class, 'delete'])->name('class-wali-kelas.delete');
        Route::get('/informasi-kelas', [ClassWaliKelasController::class, 'index'])->name('informasi-kelas');

        Route::resource('student-wali-kelas', StudentWaliKelasController::class)->parameters(['student-wali-kelas' => 'student_wali_kelas']);
        Route::delete('student-wali-kelas/{student_wali_kelas}/delete', [StudentWaliKelasController::class, 'delete'])->name('student-wali-kelas.delete');
    });

    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/data/{class}', [RekapController::class, 'getClassRekapData'])->name('rekap.data');
});
