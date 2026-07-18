<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;
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
});
