<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\UpdateAppointmentLogRequest;
use App\Http\Resources\MemberResource;
use App\Http\Responses\ApiResponse;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\User;
use App\Services\AppointmentLogService;

class AppointmentLogController extends Controller
{
    protected $logService;

    public function __construct(AppointmentLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Disaster | ดึง Disaster by id 
     */
    public function show(AppointmentLog $log)
    {
        return ApiResponse::successResponse(__('success.data'), $log);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function update(UpdateAppointmentLogRequest $request, AppointmentLog $log)
    {
        $data = $this->logService->update($log, $request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
