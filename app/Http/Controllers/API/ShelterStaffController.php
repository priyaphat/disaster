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
use Illuminate\Http\Request;

class ShelterStaffController extends Controller
{
    protected $shelterService, $shelterstaffService, $transferLogService;

    public function __construct(ShelterService $shelterService, ShelterStaffService $shelterstaffService, TransferHistoryService $transferLogService)
    {
        $this->shelterService = $shelterService;
        $this->shelterstaffService = $shelterstaffService;
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
        $data = $this->shelterstaffService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create shelter staff | เพิ่มเจ้าหน้าที่ศูนย์พักพิง
     */
    public function store(CreateShelterStaffRequest $request)
    {
        $data = $this->shelterstaffService->create($request->all());
        return ApiResponse::createdResponse(__('success.staff.created'), $data);
    }

    /**
     * Create shelter staff | เพิ่มเจ้าหน้าที่ศูนย์พักพิงรายบุคคล
     */
    public function join(CreateOneStaffRequest $request)
    {
        $data = ShelterStaff::create([
            'user_id' => $request->user_id,
            'shelter_id' => $request->shelter_id,
            'shelter_position_id' => $request->shelter_position_id,
        ]);
        return ApiResponse::createdResponse(__('success.staff.created'), $data);
    }

    /**
     * Transfer staff | ย้ายเจ้าหน้าที่ศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('shelterstaff', description: 'ส่ง id ของ shelter_staffs', type: 'string', example: '3')]
    public function transfer(TransferShelterStaffRequest $request, ShelterStaff $shelterstaff)
    {
        $oldShelter_id = $shelterstaff->shelter_id;
        $newShelter_id = $request->shelter_id;

        if ($newShelter_id == $oldShelter_id) {
            return ApiResponse::successResponse(__('success.staff.updated'));
        }

        $shelterstaff->update(['shelter_id' => $newShelter_id]);
        return ApiResponse::successResponse(__('success.staff.updated'));
    }

    /**
     * Assign position to staff | มอบหน้าที่ให้เจ้าหน้าที่ภายในศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('shelterstaff', description: 'ส่ง id ของ shelter_staffs', type: 'string', example: '3')]
    public function assignposition(AssignShelterPositionRequest $request, ShelterStaff $shelterstaff)
    {
        $position = $request->shelter_position_id;
        $shelterstaff->update(['shelter_position_id' => $position]);
        return ApiResponse::successResponse(__('success.updated'));
    }

    /**
     * Exit staff | นำเจ้าหน้าที่ออกจากศูนย์พักพิงรายบุคคล
     */
    #[PathParameter('shelterstaff', description: 'ส่ง id ของ shelter_staffs', type: 'string', example: '3')]
    public function exit(ShelterStaff $shelterstaff)
    {
        $shelterstaff->delete();
        return ApiResponse::successResponse(__('success.updated'));
    }

    /**
     * Exit all staff | นำเจ้าหน้าที่ออกจากศูนย์พักพิงทั้งหมด
     */
    public function exitall(ExitAllShelterStaffRequest $request)
    {
        $shelter_id = $request->shelter_id;
        ShelterStaff::where('shelter_id', $shelter_id)->each->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
