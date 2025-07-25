<?php

namespace App\Services;

use App\Enums\ShiftType;
use App\Http\Utility\LogUtility;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Illuminate\Support\Str;

class AppointmentLogService
{
    /**
     * Update an appointment log
     */
    public function update(AppointmentLog $log, array $data)
    {
        
    }
}
