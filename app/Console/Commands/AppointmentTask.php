<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AppointmentTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:appointment-task';
    protected $description = 'Command description';

    public function handle()
    {
        Log::info('Run task at ' . now());
    }
}
