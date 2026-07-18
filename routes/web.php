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
use App\Http\Controllers\MidtermExamsController;
use App\Http\Controllers\SettingsController;
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

    Route::get('/dashboard', function () {
        return view('auth.dashboard');
    })->name('dashboard');

    Route::get('/master-data', [SettingsController::class, 'index'])->name('master-data.index');
    Route::post('/master-data', [SettingsController::class, 'store'])->name('master-data.store');

    Route::resource('settings', SettingsController::class);
    Route::delete('settings/{setting}/delete', [SettingsController::class, 'delete'])->name('settings.delete');

    Route::resource('academic-years', AcademicYearController::class);
    Route::delete('academic-years/{academic_year}/delete', [AcademicYearController::class, 'delete'])->name('academic-years.delete');

    Route::resource('classes', ClassesController::class);
    Route::delete('classes/{class}/delete', [ClassesController::class, 'delete'])->name('classes.delete');

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
});
