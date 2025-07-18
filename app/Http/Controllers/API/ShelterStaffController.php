<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterPosition\AssignShelterPositionRequest;
use App\Http\Requests\ShelterStaff\CreateOneStaffRequest;
use App\Http\Requests\ShelterStaff\CreateShelterStaffRequest;
use App\Http\Requests\ShelterStaff\ExitAllShelterStaffRequest;
use App\Http\Requests\ShelterStaff\TransferShelterStaffRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Shelter;
use App\Models\ShelterPosition;
use App\Models\ShelterStaff;
use App\Models\TransferHistory;
use App\Services\ShelterService;
use App\Services\ShelterStaffService;
use App\Services\TransferHistoryService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ShelterStaffController extends Controller
{
    protected $shelterService, $shelterStaffService, $transferLogService;

    public function __construct(ShelterService $shelterService, ShelterStaffService $shelterStaffService, TransferHistoryService $transferLogService)
    {
        $this->shelterService = $shelterService;
        $this->shelterStaffService = $shelterStaffService;
        $this->transferLogService = $transferLogService;
    }

    /**
     * Display a listing of shelter staff| รายชื่อเจ้าหน้าที่ในศูนย์
     */
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "ไก่ ทอด")]
    #[QueryParameter('position', description: 'ค้นหาตามตำแหน่ง https://disaster-backend.gentechsoft.tech/api/docs#/operations/position.index ', type: 'string', example: "1")]
    #[QueryParameter('shelter_position', description: 'ค้นหาตามตำแหน่งในศูนย์ https://disaster-backend.gentechsoft.tech/api/docs#/operations/shelterPosition.index', type: 'string', example: "3")]
    #[QueryParameter('shelter_id', description: 'ค้นหาตามศูนย์', type: 'string', example: "2")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    public function index(Request $request)
    {
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->shelterStaffService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create shelter staff | เพิ่มเจ้าหน้าที่ศูนย์พักพิง
     */
    public function store(CreateShelterStaffRequest $request)
    {
        $data = $this->shelterStaffService->createMany($request->all());
        return ApiResponse::createdResponse(__('success.staff.joined'), $data);
    }

    /**
     * Create shelter staff | เพิ่มเจ้าหน้าที่ศูนย์พักพิงรายบุคคล
     */
    public function join(CreateOneStaffRequest $request)
    {
        $data = $this->shelterStaffService->create($request->normalized());
        return ApiResponse::createdResponse(__('success.staff.joined'), $data);
    }

    /**
     * Transfer staff | ย้ายเจ้าหน้าที่ศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('userId', description: 'ส่ง id ของ user_id', type: 'integer', example: 3)]
    public function transfer(TransferShelterStaffRequest $request, $userId)
    {

        $staff = ShelterStaff::where('user_id', $userId)->first();
        if (!$staff) {
            throw new ModelNotFoundException();
        }
        $oldShelter_id = $staff->shelter_id;
        $newShelter_id = $request->shelter_id;

        if ($newShelter_id == $oldShelter_id) {
            return ApiResponse::successResponse(__('success.staff.tranfered'));
        }

        $requestArr = ['shelter_id' => $request->shelter_id, 'user_id' => $staff->user_id, 'shelter_position_id' => $staff->shelter_position_id];
        $this->shelterStaffService->update($staff, $requestArr);
        return ApiResponse::successResponse(__('success.staff.tranfered'));
    }

    /**
     * Assign position to staff | มอบหน้าที่ให้เจ้าหน้าที่ภายในศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('userId', description: 'ส่ง id ของ user_id', type: 'integer', example: 3)]
    public function assignPosition(AssignShelterPositionRequest $request, $userId)
    {
        $staff = ShelterStaff::where('user_id', $userId)->first();
        if (!$staff) {
            throw new ModelNotFoundException();
        }

        $requestArr = ['shelter_id' => $staff->shelter_id, 'user_id' => $staff->user_id, 'shelter_position_id' => $request->shelter_position_id];
        $this->shelterStaffService->update($staff, $requestArr);
        return ApiResponse::successResponse(__('success.staff.assigned'));
    }

    /**
     * Exit staff | นำเจ้าหน้าที่ออกจากศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('userId', description: 'ส่ง id ของ user_id', type: 'integer', example: 3)]
    public function exit($userId)
    {
        $staff = ShelterStaff::where('user_id', $userId)->first();
        if (!$staff) {
            throw new ModelNotFoundException();
        }
        $staff->delete();
        return ApiResponse::successResponse(__('success.staff.leaved'));
    }

    /**
     * Exit all staff | นำเจ้าหน้าที่ออกจากศูนย์พักพิงทั้งหมด
     */
    public function exitAll(ExitAllShelterStaffRequest $request)
    {
        $shelter_id = $request->shelter_id;
        ShelterStaff::where('shelter_id', $shelter_id)->cursor()->each->delete();
        return ApiResponse::successResponse(__('success.staff.leaved'));
    }
}
