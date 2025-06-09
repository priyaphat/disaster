<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }
    /**
     * Display a listing of the an appointment | แสดงรายการนัดหมาย
     */
    public function index(Request $request)
    {
        //
        $data = $this->appointmentService->index($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create an appointment | สร้างนัดหมาย
     */
    public function store(StoreAppointmentRequest $request)
    {
        $data = $this->appointmentService->create($request->normalized());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Show an appointment | แสดงนัดหมาย
     */
    public function show(Appointment $appointment)
    {
        return ApiResponse::successResponse(__('success.data'), $appointment);
    }

    /**
     * Show an appointment | แก้ไขนัดหมาย
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $data = $this->appointmentService->update($appointment, $request->normalized());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Delete an appointment | ลบนัดหมาย
     */
    public function destroy(Appointment $appointment)
    {
        //
        $this->appointmentService->delete($appointment);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
