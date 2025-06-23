<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyRequest;
use App\Services\EmergencyRequestService;
use App\Http\Requests\EmergencyRequestRequest;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

class EmergencyRequestController extends Controller
{
    protected $emergencyRequestService;

    public function __construct(EmergencyRequestService $emergencyRequestService)
    {
        $this->emergencyRequestService = $emergencyRequestService;
    }

    /**
     * Emergency request | ดึงข้อมูลทุกตัว / ปุ้ย .
     */
    public function index()
    {
        $emergencyRequest = $this->emergencyRequestService->index();
        return ApiResponse::successResponse(__('success.data'), $emergencyRequest);
    }

    /**
     * Emergency request | ดึงข้อมูล อนุมัติ / ปุ้ย .
     */
    public function show(Request $emergencyRequest)
    {
        // approve
        $data = $this->emergencyRequestService->show($emergencyRequest, null);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึงข้อมูล รอ , ช่วยเหลือ ตัวแปร / ปุ้ย .
     */
    public function show_wait(Request $emergencyRequest)
    {
        // waiting , helping
        $data = $this->emergencyRequestService->show($emergencyRequest, 'waiting');
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึงข้อมูล เสร็จ , ไม่เสร็จ , ไม่อนุมัติ  ตัวแปร / ปุ้ย .
     */
    public function show_history(Request $emergencyRequest)
    {
        // complete , disapprove , incomplete
        $data = $this->emergencyRequestService->show($emergencyRequest, 'history');
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึงข้อมูล id , phone  .
     */
    #[PathParameter('emc', description: 'Phone or id | เบอร์โทรหรือ id', type: 'string', example: '0987654321')]
    public function emcRequestBy(Request $request, $emc = null)
    {
        $data = $this->emergencyRequestService->emcRequestBy($request, $emc); //phone & id
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | สร้างรายการแจ้งเหตุฉุกเฉิน  ตัวแปร / ปุ้ย .
     */
    public function store(EmergencyRequestRequest $request)
    {
        $data = $this->emergencyRequestService->create($request->all());
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | อัพเดตรายการแจ้งเหตุฉุกเฉิน  ตัวแปร / ปุ้ย .
     */
    public function update(EmergencyRequestRequest $request, EmergencyRequest $emergencyRequest)
    {
        $data = $this->emergencyRequestService->update($emergencyRequest, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Emergency request | ลบ  ตัวแปร / ปุ้ย .
     */
    public function destroy(EmergencyRequest $emergencyRequest)
    {
        $emergencyRequest->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

    /**
     * Emergency request | ดึง เหตุผล.
     */
    public function reason()
    {
        $data = $this->emergencyRequestService->reason();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | ดึง Noti. mock
     */
    public function noti()
    {
        $data = $this->emergencyRequestService->noti();
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
