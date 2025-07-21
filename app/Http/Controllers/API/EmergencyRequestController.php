<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyRequest;
use App\Services\EmergencyRequestService;
use App\Http\Requests\EmergencyRequestRequest;
use App\Http\Utility\LinePostUtility;
use App\Models\Prefix;
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
    public function emcRequestBy(Request $request, $emc = null, $id = null)
    {
        $data = $this->emergencyRequestService->emcRequestBy($request, $emc, $id); //phone & id
        return ApiResponse::successResponse(__('success.data'), $data);
    }
    /**
     * Emergency request | สร้างรายการแจ้งเหตุฉุกเฉิน  ตัวแปร / ปุ้ย .
     */
    public function store(EmergencyRequestRequest $request)
    {
        $data = $this->emergencyRequestService->create($request->all());
        $name = ['name' => Prefix::where('id', $data['prefix_id'])->value('name') . ' ' . $data['first_name'] . ' ' . $data['last_name']];
        (new LineController)->LineNotification($name, LineController::Emc_request_post);
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    /**
     * Emergency request | อัพเดตรายการแจ้งเหตุฉุกเฉิน  ตัวแปร / ปุ้ย .
     */
    public function update(EmergencyRequestRequest $request, EmergencyRequest $emergencyRequest)
    {
        if ($edit = self::canEdit($emergencyRequest)) return $edit;

        $old = clone $emergencyRequest;
        $skip = false;

        if ($old['emergency_team_id'] && !$request['emergency_team_id']) { //ส่วนกลาง กดลบทีม
            //team = null
            if ($res = self::canCheck($request)) return $res;
            $request['status'] = EmergencyRequest::waiting;
            $skip = true;
            LinePostUtility::LineMakeInformation(LineController::Emc_request_end, $emergencyRequest);
        } else if (
            $old['emergency_team_id']
            && $request['emergency_team_id']
            && $old['emergency_team_id'] != $request['emergency_team_id']  //ส่วนกลาง กดเปลี่ยนทีม
        ) {
            if ($res = self::canCheck($request)) return $res;
            //ส่งแจ้งเตือน หาหัวหน้าทีมเก่า
            LinePostUtility::LineMakeInformation(LineController::Emc_request_end, $emergencyRequest);
            //ส่งหาหัวหน้าทีมใหม่ 
            $request['assigner'] = Auth::user()->id;
            LinePostUtility::LineMakeInformation(LineController::Emc_request_post_team, $request);
        }

        $this->emergencyRequestService->update($emergencyRequest, $request->all());

        if ($request->status != $old['status']) {
            if ($emergencyRequest['status'] == EmergencyRequest::waiting && $skip == false) { //หัวหน้าทีม ปฏิเสธการช่วยเหลือ
                LinePostUtility::LineMakeInformation(LineController::Emc_request_unaccept, $emergencyRequest);
                $emergencyRequest->update(['emergency_team_id' => null]);
            } else if ($emergencyRequest['status'] == EmergencyRequest::approve) { //ส่งให้หัวหน้าทีมกด อนุมัติช่วยเหลือ
                LinePostUtility::LineMakeInformation(LineController::Emc_request_post_team, $emergencyRequest);
            } else if ($emergencyRequest['status'] == EmergencyRequest::helping) { //ส่งแจ้งเตือนให้ลูกทีมทราบ
                LinePostUtility::LineMakeInformation(LineController::Emc_assigned, $emergencyRequest);
            } else if ($emergencyRequest['status'] == EmergencyRequest::complete) { //ส่งแจ้งเตือน ช่วยเหลือสำเร็จ
                LinePostUtility::LineMakeInformation(LineController::Emc_success, $emergencyRequest);
            } else if ($emergencyRequest['status'] == EmergencyRequest::incomplete) { //ส่งแจ้งเตือน ช่วยเหลือไม่สำเร็จ
                LinePostUtility::LineMakeInformation(LineController::Emc_unsuccess, $emergencyRequest);
            }
        }

        return ApiResponse::successResponse(__('success.updated'), $emergencyRequest);
    }

    private function canCheck($e)
    {
        if (!in_array($e['status'], [EmergencyRequest::waiting, EmergencyRequest::approve]))
            return ApiResponse::errorResponse(__('error.emc_req.woking_not_change'));
    }

    private function canEdit($e)
    {
        if (in_array($e['status'], [EmergencyRequest::disapprove, EmergencyRequest::incomplete, EmergencyRequest::complete]))
            return ApiResponse::errorResponse(__('error.emc_req.end_of_job'));
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

    public function phone_detail(Request $request, $phone, $id)
    {
        $data = $this->emergencyRequestService->emcRequestBy($request, $phone, $id); //phone & id
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
