<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule correspondence reminders
Schedule::command('correspondence:send-reminders')
    ->dailyAt('09:00')
    ->timezone('Asia/Riyadh')
    ->description('Send follow-up reminders for correspondences');
