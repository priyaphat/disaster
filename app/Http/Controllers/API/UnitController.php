<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Unit\CreateUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index(Request $request)
    {
        $data = $this->unitService->index($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(CreateUnitRequest $request)
    {
        $data = $this->unitService->create($request->all());
        return ApiResponse::createdResponse(__('success.unit.created'), $data);
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $data = $this->unitService->update($unit, $request->all());
        return ApiResponse::successResponse(__('success.unit.updated'), $data);
    }

    public function destroy(Unit $unit)
    {
        if (Product::where('unit_id', $unit->id)->first()) throw new BadRequestHttpException(__('error.sql.fk-constraint'));
        $unit->delete();
        return ApiResponse::successResponse(__('success.unit.deleted'));
    }
}
