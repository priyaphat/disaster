<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Population\CreatePopulationRequest;
use App\Http\Requests\Population\TransferPopulationRequest;
use App\Http\Requests\Population\UpdatePopulationRequest;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\TransferHistory;
use App\Services\PopulationService;
use App\Services\ShelterService;
use App\Services\TransferHistoryService;

class PopulationController extends Controller
{
    protected $populationService, $shelterService, $transferLogService;

    public function __construct(PopulationService $populationService, ShelterService $shelterService, TransferHistoryService $transferLogService)
    {
        $this->populationService = $populationService;
        $this->shelterService = $shelterService;
        $this->transferLogService = $transferLogService;
    }

    /**
     * Display a listing of the team | แสดงรายชื่อประชากร
     */
    public function index(Request $request)
    {
        $data = $this->populationService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create population | สร้างประชากรใหม่
     */
    public function store(CreatePopulationRequest $request)
    {
        if ($this->populationService->isShelterMax($request->shelter_id)) {
            return ApiResponse::errorResponse(__('ศูนย์พักพิงเต็ม'));
        }
        $data = $this->populationService->create($request->all());
        $this->transferLogService->createLog(TransferHistory::ENTER, Population::class, $data->id, Shelter::class, $data->shelter_id);
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    /**
     * Display specific population | แสดงประชากรรายบุคคล
     */
    public function show(Population $population)
    {
        return ApiResponse::successResponse(__('success.data'), $population);
    }

    /**
     * Update population | อัพเดตข้อมูลประชากรรายบุคคล
     */
    public function update(UpdatePopulationRequest $request, Population $population)
    {
        $data = $this->populationService->update($population, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Transfer population | ย้ายศูนย์พักพิงประชากรรายบุคคล
     */
    public function transfer(TransferPopulationRequest $request, Population $population)
    {
        $oldShelter_id = $population->shelter_id;
        $population_id = $population->id;
        $newShelter_id = $request->shelter_id;

        if ($newShelter_id == $oldShelter_id) {
            return ApiResponse::successResponse(__('success.updated'));
        }

        $shelter = $this->shelterService->getShelter($newShelter_id);
        $data = $this->populationService->transfer($population, $request->all(), $shelter->max_capacity);
        if ($data['shelter_id'] == $oldShelter_id) {
            return ApiResponse::errorResponse(__('ศูนย์พักพิงเต็ม'));
        } else {
            $this->transferLogService->createLog(TransferHistory::TRANSFER, Population::class, $population_id, Shelter::class, $newShelter_id);
        }
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Exit population | นำประชากรออกจากศูนย์พักพิงรายบุคคล
     */
    public function exit(Population $population)
    {
        $oldShelter_id = $population->shelter_id;
        $population_id = $population->id;

        $request = ['shelter_id' => Population::exit];
        $data = $this->populationService->update($population, $request);
        $this->transferLogService->createLog(TransferHistory::EXIT, Population::class, $population_id, Shelter::class, $oldShelter_id);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
