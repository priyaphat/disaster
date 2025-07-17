<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\SearchAppointmentRequest;
use App\Http\Requests\Appointment\SearchCalendarRequest;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Appointment;
use App\Services\AppointmentService;

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
    public function index(SearchAppointmentRequest $request)
    {
        $data = $this->appointmentService->index($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create an appointment | สร้างนัดหมาย
     */
    public function store(StoreAppointmentRequest $request)
    {
        $data = $this->appointmentService->create($request->all());
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
        $data = $this->appointmentService->update($appointment, $request->all());
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

    /**
     * Display by date | แสดงรายการนัดหมายโดยวันที่
     * 
     * ถ้าแสดงนัดหมายทั้งหมด ไม่ต้องส่ง `shelter_id` และ `team_id` | When display all appointments, do not send `shelter_id` and `team_id`
     * 
     * ถ้าแสดงนัดหมายแยกตามทีม ไม่ต้องส่ง `shelter_id` | When display appointments by team, do not send `shelter_id`
     * 
     * ถ้าแสดงนัดหมายแยกตามศูนย์พักพิง ไม่ต้องส่ง `team_id` | When display appointments by  shelter, do not `send team_id`
     */
    public function getDailySchedule(SearchCalendarRequest $request)
    {
        //
        $data = $this->appointmentService->getDailySchedule($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Display with calendar or table | แสดงรายการนัดหมายแบบปฏิทินหรือตาราง
     * 
     * ถ้าแสดงนัดหมายทั้งหมด ไม่ต้องส่ง `shelter_id` และ `team_id` | When display all appointments, do not send `shelter_id` and `team_id`
     * 
     * ถ้าแสดงนัดหมายแยกตามทีม ไม่ต้องส่ง `shelter_id` | When display appointments by team, do not send `shelter_id`
     * 
     * ถ้าแสดงนัดหมายแยกตามศูนย์พักพิง ไม่ต้องส่ง `team_id` | When display appointments by  shelter, do not `send team_id`
     */
    public function getMonthSchedule(SearchAppointmentRequest $request)
    {
        $data = $this->appointmentService->getMonthSchedule($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
