<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AssignmentMeetingsController;
use App\Http\Controllers\AssignmentScoresController;
use App\Http\Controllers\AttendanceMeetingsController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\DailyTestMeetingsController;
use App\Http\Controllers\DailyTestScoresController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinalExamsController;
use App\Http\Controllers\FinalScoresController;
use App\Http\Controllers\MidtermExamsController;
use App\Http\Controllers\MidtermScoresController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SettingsWaliKelasController;
use App\Http\Controllers\StudentsController;
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

    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/data/{class}', [RekapController::class, 'getClassRekapData'])->name('rekap.data');
});
