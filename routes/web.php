<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AttendanceMeetingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassesController;
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
});
