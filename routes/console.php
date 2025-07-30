<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::command('app:weather-task')->everySixHours();
Schedule::command('app:appointment-task')->everyMinute();
Schedule::command('app:notification-task')->everyMinute();
// Schedule::command('app:appointment-task')->timezone('Asia/Bangkok')->dailyAt('23:59');
