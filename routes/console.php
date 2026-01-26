<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Donation Followup Reminder - Manual via cPanel (Setiap 3 hari)
// Schedule::command('donations:send-followup')->cron('0 9 */3 * *');
