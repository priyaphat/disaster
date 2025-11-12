<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Schedule::command('app:weather-task')->everySixHours();
Schedule::command('app:appointment-task')->everyMinute();
Schedule::command('app:sample-task')->everyMinute();
Schedule::command('app:point-task')->dailyAt('23:59');
Schedule::command('app:notification-task')->dailyAt('23:59');
// Schedule::command('app:appointment-task')->timezone('Asia/Bangkok')->dailyAt('23:59');
Schedule::command('app:smog-task')->dailyAt('12:00');
Schedule::command('app:earthquake-task')->dailyAt('12:00');
Schedule::command('app:wildfire-task')->dailyAt('12:00');
Schedule::command('app:line-task')->dailyAt('00:00');
Schedule::command('app:flood-task')->dailyAt('12:00');

Schedule::command('app:snapshot-shelter-task')->dailyAt('23:59');
Schedule::command('app:snapshot-population-task')->dailyAt('23:59');