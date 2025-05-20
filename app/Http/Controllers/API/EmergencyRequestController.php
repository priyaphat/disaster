<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyRequest;
use App\Services\EmergencyRequestService;
use App\Http\Requests\EmergencyRequestRequest;

class EmergencyRequestController extends Controller
{
    protected $emergencyRequestService;

    public function __construct(EmergencyRequestService $emergencyRequestService)
    {
        $this->emergencyRequestService = $emergencyRequestService;
    }

    public function index()
    {
        $emergencyRequest = $this->emergencyRequestService->index();
        return ApiResponse::successResponse(__('success.data'), $emergencyRequest);
    }

    public function show($emergencyRequest)
    {
        $data = $this->emergencyRequestService->show($emergencyRequest);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(EmergencyRequestRequest $request)
    {
        $data = $this->emergencyRequestService->create($request->all());
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    public function update(EmergencyRequestRequest $request, EmergencyRequest $emergencyRequest)
    {
        $data = $this->emergencyRequestService->update($emergencyRequest, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
    
    public function destroy(EmergencyRequest $emergencyRequest)
    {
        $emergencyRequest->delete();
        return ApiResponse::successResponse(__('success.deleted'));
    }

}
