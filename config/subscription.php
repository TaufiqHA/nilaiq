<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Free Trial
    |--------------------------------------------------------------------------
    |
    | Number of days every newly registered teacher receives full access
    | before a paid subscription is required.
    |
    */

    'trial_days' => (int) env('SUBSCRIPTION_TRIAL_DAYS', 3),

    /*
    |--------------------------------------------------------------------------
    | Renewal Window
    |--------------------------------------------------------------------------
    |
    | Number of days before expiry when an active subscription may be renewed.
    |
    */

    'renewal_window_days' => (int) env('SUBSCRIPTION_RENEWAL_WINDOW_DAYS', 5),

    /*
    |--------------------------------------------------------------------------
    | Renewal Reminders
    |--------------------------------------------------------------------------
    |
    | Number of days before expiry that the reminder notification is sent.
    |
    */

    'reminder_days' => [7, 3, 1],

];
