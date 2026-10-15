<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AttendanceMeetingsController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::get('/health', [AuthController::class, 'health']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated API Routes (Guru Mapel only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'mapel'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Master data
    Route::get('/classes', [ClassesController::class, 'index']);

    // Attendance recap
    Route::get('/rekap-absensi', [AttendanceMeetingsController::class, 'rekapAbsensi']);

    // Attendance meetings
    Route::get('/attendance-meetings', [AttendanceMeetingsController::class, 'index']);
    Route::post('/attendance-meetings', [AttendanceMeetingsController::class, 'store']);
    Route::get('/attendance-meetings/{attendanceMeeting}', [AttendanceMeetingsController::class, 'show']);
    Route::put('/attendance-meetings/{attendanceMeeting}', [AttendanceMeetingsController::class, 'update']);
    Route::delete('/attendance-meetings/{attendanceMeeting}', [AttendanceMeetingsController::class, 'delete']);

    // Attendances
    Route::get('/attendances', [AttendancesController::class, 'index']);
    Route::post('/attendances', [AttendancesController::class, 'store']);
    Route::get('/attendances/{attendance}', [AttendancesController::class, 'show']);
    Route::put('/attendances/{attendance}', [AttendancesController::class, 'update']);
    Route::delete('/attendances/{attendance}', [AttendancesController::class, 'delete']);
});
