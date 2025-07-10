<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\AppointmentLogController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AppointmentTask extends Command
{
    protected $signature = 'app:appointment-task';
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        Log::info("Create An Appointment Schedule Log");
        $appointmentController = new AppointmentLogController();
        $appointmentController->store();
    }
}
