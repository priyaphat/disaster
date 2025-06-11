<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\EmergencyRequest;
use App\Services\EmergencyRequestService;
use App\Http\Requests\EmergencyRequestRequest;
use App\Enums\CancellationReason;

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

    public function show(Request $emergencyRequest)
    {
        // approve
        $data = $this->emergencyRequestService->show($emergencyRequest, null);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function show_wait(Request $emergencyRequest)
    {
        // waiting , helping
        $data = $this->emergencyRequestService->show($emergencyRequest, 'waiting');
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function show_history(Request $emergencyRequest)
    {
        // complete , disapprove , incomplete
        $data = $this->emergencyRequestService->show($emergencyRequest, 'history');
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function emcRequestBy(Request $request, $emc=null)
    {
        $data = $this->emergencyRequestService->emcRequestBy($request, $emc); //phone & id
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

    public function reason()
    {
        $data = $this->emergencyRequestService->reason();
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
