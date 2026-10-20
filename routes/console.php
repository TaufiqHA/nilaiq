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

// Subscription lifecycle
Schedule::command('subscriptions:expire')->dailyAt('00:05');
Schedule::command('subscriptions:send-reminders')->dailyAt('08:00');

// Reconcile payments in case the Midtrans webhook is delayed or unreachable.
Schedule::command('orders:sync-pending')->everyTenMinutes()->withoutOverlapping();
