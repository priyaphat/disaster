<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Position\FilterPositionRequest;
use App\Http\Requests\Position\StorePositionRequest;
use App\Http\Requests\Position\UpdatePositionRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Position;
use App\Services\PositionService;

class PositionController extends Controller
{
    protected $positionService;

    public function __construct(PositionService $positionService)
    {
        $this->positionService = $positionService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(FilterPositionRequest $request)
    {
        //
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->positionService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        //
        $data = $this->positionService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        return ApiResponse::successResponse(__('success.data'), $position);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        //
        $data = $this->positionService->update($position, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        //
        $this->positionService->delete($position);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
