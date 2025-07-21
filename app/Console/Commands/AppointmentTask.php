<?php

namespace App\Console\Commands;

use App\Enums\ShiftType;
use App\Http\Controllers\API\AppointmentLogController;
use App\Http\Utility\LogUtility;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
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
        $base = Carbon::today();
        $morningTime = $base->copy()->addHours(14)->addMinutes(0);
        $shift  = Carbon::now()->lessThan($morningTime) ? ShiftType::MORNING->value : ShiftType::AFTERNOON->value;
        LogUtility::createAppointmentLog(['shift' => $shift]);
        Log::info('Save appointment logs : ' . $shift);
    }
}
