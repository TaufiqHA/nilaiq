<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AssignmentMeetingsController;
use App\Http\Controllers\AssignmentScoresController;
use App\Http\Controllers\AttendanceMeetingsController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\DailyTestMeetingsController;
use App\Http\Controllers\DailyTestScoresController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinalExamsController;
use App\Http\Controllers\FinalScoresController;
use App\Http\Controllers\MidtermExamsController;
use App\Http\Controllers\MidtermScoresController;
use App\Http\Controllers\ScoreScanController;
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

    // Score scan (AI)
    Route::post('/score-scan', [ScoreScanController::class, 'scan']);

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

    // Daily test meetings
    Route::get('/daily-test-meetings', [DailyTestMeetingsController::class, 'index']);
    Route::post('/daily-test-meetings', [DailyTestMeetingsController::class, 'store']);
    Route::get('/daily-test-meetings/{dailyTestMeeting}', [DailyTestMeetingsController::class, 'show']);
    Route::put('/daily-test-meetings/{dailyTestMeeting}', [DailyTestMeetingsController::class, 'update']);
    Route::delete('/daily-test-meetings/{dailyTestMeeting}', [DailyTestMeetingsController::class, 'delete']);

    // Daily test scores
    Route::get('/daily-test-scores', [DailyTestScoresController::class, 'index']);
    Route::post('/daily-test-scores', [DailyTestScoresController::class, 'store']);
    Route::get('/daily-test-scores/{dailyTestScore}', [DailyTestScoresController::class, 'show']);
    Route::put('/daily-test-scores/{dailyTestScore}', [DailyTestScoresController::class, 'update']);
    Route::delete('/daily-test-scores/{dailyTestScore}', [DailyTestScoresController::class, 'delete']);

    // Assignment meetings
    Route::get('/assignment-meetings', [AssignmentMeetingsController::class, 'index']);
    Route::post('/assignment-meetings', [AssignmentMeetingsController::class, 'store']);
    Route::get('/assignment-meetings/{assignmentMeeting}', [AssignmentMeetingsController::class, 'show']);
    Route::put('/assignment-meetings/{assignmentMeeting}', [AssignmentMeetingsController::class, 'update']);
    Route::delete('/assignment-meetings/{assignmentMeeting}', [AssignmentMeetingsController::class, 'delete']);

    // Assignment scores
    Route::get('/assignment-scores', [AssignmentScoresController::class, 'index']);
    Route::post('/assignment-scores', [AssignmentScoresController::class, 'store']);
    Route::get('/assignment-scores/{assignmentScore}', [AssignmentScoresController::class, 'show']);
    Route::put('/assignment-scores/{assignmentScore}', [AssignmentScoresController::class, 'update']);
    Route::delete('/assignment-scores/{assignmentScore}', [AssignmentScoresController::class, 'delete']);

    // Midterm exams (PTS)
    Route::get('/midterm-exams', [MidtermExamsController::class, 'index']);
    Route::post('/midterm-exams', [MidtermExamsController::class, 'store']);
    Route::get('/midterm-exams/{midtermExam}', [MidtermExamsController::class, 'show']);
    Route::put('/midterm-exams/{midtermExam}', [MidtermExamsController::class, 'update']);
    Route::delete('/midterm-exams/{midtermExam}', [MidtermExamsController::class, 'delete']);

    // Midterm scores
    Route::get('/midterm-scores', [MidtermScoresController::class, 'index']);
    Route::post('/midterm-scores', [MidtermScoresController::class, 'store']);
    Route::get('/midterm-scores/{midtermScore}', [MidtermScoresController::class, 'show']);
    Route::put('/midterm-scores/{midtermScore}', [MidtermScoresController::class, 'update']);
    Route::delete('/midterm-scores/{midtermScore}', [MidtermScoresController::class, 'delete']);

    // Final exams (PAS)
    Route::get('/final-exams', [FinalExamsController::class, 'index']);
    Route::post('/final-exams', [FinalExamsController::class, 'store']);
    Route::get('/final-exams/{finalExam}', [FinalExamsController::class, 'show']);
    Route::put('/final-exams/{finalExam}', [FinalExamsController::class, 'update']);
    Route::delete('/final-exams/{finalExam}', [FinalExamsController::class, 'delete']);

    // Final scores
    Route::get('/final-scores', [FinalScoresController::class, 'index']);
    Route::post('/final-scores', [FinalScoresController::class, 'store']);
    Route::get('/final-scores/{finalScore}', [FinalScoresController::class, 'show']);
    Route::put('/final-scores/{finalScore}', [FinalScoresController::class, 'update']);
    Route::delete('/final-scores/{finalScore}', [FinalScoresController::class, 'delete']);
});
