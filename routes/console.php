<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the financial reports to check for due users every minute.
// The command 'reports:send' uses chunks to dispatch jobs for users who are due.
Schedule::command('reports:send')->everyMinute();
