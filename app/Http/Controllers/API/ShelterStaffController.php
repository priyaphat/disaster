<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterStaff\CreateShelterStaffRequest;
use App\Http\Requests\ShelterStaff\TransferShelterStaffRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Shelter;
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

    public function store(CreateShelterStaffRequest $request)
    {
        $data = $this->shelterstaffService->create($request->all());
        return ApiResponse::createdResponse('Success', $data);
    }

    /**
     * Transfer population | ย้ายศูนย์พักพิงประชากรรายบุคคล
     */
    public function transfer(TransferShelterStaffRequest $request, ShelterStaff $shelterstaff)
    {
        $oldShelter_id = $shelterstaff->shelter_id;
        $user_id = $shelterstaff->user_id;
        $newShelter_id = $request->shelter_id;

        if ($newShelter_id == $oldShelter_id) {
            return ApiResponse::successResponse(__('success.updated'));
        }

        // $shelter = $this->shelterService->getShelter($newShelter_id);
        // $data = $this->shelterstaffService->transfer($shelterstaff, $request->all(), $shelter->max_capacity);
        $data = $shelterstaff->update(['shelter_id' => $newShelter_id, 'shelter_position_id' => null]);
        // if ($data['shelter_id'] == $oldShelter_id) {
        //     return ApiResponse::errorResponse(__('ศูนย์พักพิงเต็ม'));
        // } else {
        //     $this->transferLogService->createLog(TransferHistory::TRANSFER, ShelterStaff::class, $population_id, Shelter::class, $newShelter_id);
        // }
        $this->transferLogService->createLog(TransferHistory::TRANSFER, ShelterStaff::class, $user_id, Shelter::class, $newShelter_id);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Exit staff | นำเจ้าหน้าที่ออกจากศูนย์พักพิงรายบุคคล
     */
    public function exit(ShelterStaff $shelterstaff)
    {
        $oldShelter_id = $shelterstaff->shelter_id;
        $user_id = $shelterstaff->user_id;

        $data = $shelterstaff->delete();
        $this->transferLogService->createLog(TransferHistory::EXIT, ShelterStaff::class, $user_id, Shelter::class, $oldShelter_id);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
