<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Unit\CreateUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UnitController extends Controller
{
    use AuthorizesRequests;
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Unit::class);
        $data = $this->unitService->index($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(CreateUnitRequest $request)
    {
        $this->authorize('create', Unit::class);
        $data = $this->unitService->create($request->all());
        return ApiResponse::createdResponse(__('success.unit.created'), $data);
    }

    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $this->authorize('update', Unit::class);
        $data = $this->unitService->update($unit, $request->all());
        return ApiResponse::successResponse(__('success.unit.updated'), $data);
    }

    public function destroy(Unit $unit)
    {
        $this->authorize('delete', Unit::class);
        if (Product::where('unit_id', $unit->id)->first()) throw new BadRequestHttpException(__('error.sql.fk-constraint2'));
        $unit->delete();
        return ApiResponse::successResponse(__('success.unit.deleted'));
    }
}
