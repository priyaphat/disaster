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
    protected $shelterPositionService;

    public function __construct(ShelterPositionService $shelterPositionService)
    {
        $this->shelterPositionService = $shelterPositionService;
    }
    
    /**
     * Display a listing of position in shelter| แสดงรายการตำแน่งในศูนย์พักพิง
     */
    public function index()
    {
        //
        $data = $this->shelterPositionService->getList();
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Created shelter position| สร้างตำแหน่งในศูนย์พักพิง
     */
    public function store(CreateShelterPositionRequest $request)
    {
        //
        $data = $this->shelterPositionService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Update the specified shelter position| แก้ไขชื่อตำแหน่ง
     */
    #[PathParameter('shelterPosition', description: 'ส่ง id ของ shelter_positions', type: 'string', example: '3')]
    public function update(UpdateShelterPositionRequest $request, ShelterPosition $shelterPosition)
    {
        //
        $data = $this->shelterPositionService->update($shelterPosition, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified shelter  position| ลบตำแหน่งภายในศูนย์
     */
    #[PathParameter('shelterPosition', description: 'ส่ง id ของ shelter_positions', type: 'string', example: '5')]
    public function destroy(ShelterPosition $shelterPosition)
    {
        //
        $this->shelterPositionService->delete($shelterPosition);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
