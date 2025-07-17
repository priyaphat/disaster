<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::command('app:weather-task')->everySixHours();
Schedule::command('app:appointment-task')->dailyAt('13:00');
Schedule::command('app:appointment-task')->dailyAt('23:59');