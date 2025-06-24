<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterPosition\CreateShelterPositionRequest;
use App\Http\Requests\ShelterPosition\UpdateShelterPositionRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ShelterPosition;
use App\Services\ShelterPositionService;
use Dedoc\Scramble\Attributes\PathParameter;
use Illuminate\Http\Request;

class ShelterPositionController extends Controller
{
    protected $shelterpositionService;

    public function __construct(ShelterPositionService $shelterpositionService)
    {
        $this->shelterpositionService = $shelterpositionService;
    }
    
    /**
     * Display a listing of position in shelter| แสดงรายการตำแน่งในศูนย์พักพิง
     */
    public function index()
    {
        //
        $data = $this->shelterpositionService->getList();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Created shelter position| สร้างตำแหน่งในศูนย์พักพิง
     */
    public function store(CreateShelterPositionRequest $request)
    {
        //
        $data = $this->shelterpositionService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Update the specified shelter position| แก้ไขชื่อตำแหน่ง
     */
    #[PathParameter('shelterposition', description: 'ส่ง id ของ shelter_positions', type: 'string', example: '3')]
    public function update(UpdateShelterPositionRequest $request, ShelterPosition $shelterposition)
    {
        //
        $data = $this->shelterpositionService->update($shelterposition, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified shelter  position| ลบตำแหน่งภายในศูนย์
     */
    #[PathParameter('shelterposition', description: 'ส่ง id ของ shelter_positions', type: 'string', example: '5')]
    public function destroy(ShelterPosition $shelterposition)
    {
        //
        $this->shelterpositionService->delete($shelterposition);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
