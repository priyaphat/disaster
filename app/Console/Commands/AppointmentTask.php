<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AppointmentTask extends Command
{
    protected $signature = 'cron:appointment-task';
    protected $description = 'Command description';

    public function handle()
    {
        Log::info('Run task at ' . now());
        return Command::SUCCESS;
    }
}
