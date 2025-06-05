<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Unit\CreateUnitRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index()
    {
        $data = $this->unitService->index();
        return ApiResponse::successResponse(__('success.data'), $data);
    }
    
    public function store(CreateUnitRequest $request)
    {
        $data = $this->unitService->create($request->all());
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $this->unitService->update($unit, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
    
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
