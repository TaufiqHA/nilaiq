<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule backup cleanup daily at midnight
Schedule::command('backup:clean')->dailyAt('00:00')->runInBackground();

// Schedule database backup daily at 00:10 AM
Schedule::command('backup:run --only-db')->dailyAt('00:10')->runInBackground();
