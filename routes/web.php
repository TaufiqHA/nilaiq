<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpreadsheetController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/export/{id}', [SpreadsheetController::class, 'export']);