<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterPosition\AssignShelterPositionRequest;
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
        //Craete transfer log for each staff
        foreach ($data as $staff) {
            $this->transferLogService->createLog(TransferHistory::ENTER, ShelterStaff::class, $staff->id, Shelter::class, $staff->shelter_id);
        }
        return ApiResponse::createdResponse('Success', $data);
    }

    /**
     * Transfer staff | ย้ายเจ้าหน้าที่ศูนย์พักพิงรายบุคคล
     */
    public function transfer(TransferShelterStaffRequest $request, ShelterStaff $shelterstaff)
    {
        $oldShelter_id = $shelterstaff->shelter_id;
        $shelterstaff_id = $shelterstaff->id;
        $newShelter_id = $request->shelter_id;

        if ($newShelter_id == $oldShelter_id) {
            return ApiResponse::successResponse(__('success.updated'));
        }

        $shelterstaff->update(['shelter_id' => $newShelter_id, 'shelter_position_id' => null]);
        $this->transferLogService->createLog(TransferHistory::TRANSFER, ShelterStaff::class, $shelterstaff_id, Shelter::class, $newShelter_id);
        return ApiResponse::successResponse(__('success.updated'));
    }

    /**
     * Assign position to staff | มอบหน้าที่ให้เจ้าหน้าที่ภายในศูนย์พักพิงรายบุคคล
     */
    public function assignposition(AssignShelterPositionRequest $request, ShelterStaff $shelterstaff)
    {
        $position = $request->shelter_position_id;
        $shelterstaff_id = $shelterstaff->id;

        $shelterstaff->update(['shelter_position_id' => $position]);
        $this->transferLogService->createLog(TransferHistory::ASSIGN, ShelterStaff::class, $shelterstaff_id, ShelterPosition::class, $position);
        return ApiResponse::successResponse(__('success.updated'));
    }

    /**
     * Exit staff | นำเจ้าหน้าที่ออกจากศูนย์พักพิงรายบุคคล
     */
    public function exit(ShelterStaff $shelterstaff)
    {
        $oldShelter_id = $shelterstaff->shelter_id;
        $shelterstaff_id = $shelterstaff->id;

        $shelterstaff->update(['shelter_id' => null, 'shelter_position_id' => null]);
        $this->transferLogService->createLog(TransferHistory::EXIT, ShelterStaff::class, $shelterstaff_id, Shelter::class, $oldShelter_id);
        return ApiResponse::successResponse(__('success.updated'));
    }

    /**
     * Exit all staff | นำเจ้าหน้าที่ออกจากศูนย์พักพิงทั้งหมด
     */
    public function exitall(ExitAllShelterStaffRequest $request)
    {
        $shelter_id = $request->shelter_id;
        $shelterstaff = ShelterStaff::where('shelter_id', $shelter_id)->get();
        ShelterStaff::where('shelter_id', $shelter_id)->update(['shelter_id' => null, 'shelter_position_id' => null]);
        foreach ($shelterstaff as $staff) {
            $this->transferLogService->createLog(TransferHistory::EXIT, ShelterStaff::class, $staff->id, Shelter::class, $shelter_id);
        }

        return ApiResponse::successResponse(__('success.updated'));
    }
}
