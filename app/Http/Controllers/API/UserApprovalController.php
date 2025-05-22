<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserApproval\ApprovalRequest;
use App\Http\Responses\ApiResponse;
use App\Models\UserApproval as ApprovaL;
use App\Services\UserApprovalService as ApprovalService;

class UserApprovalController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = $this->approvalService->paginate([]);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Approval $approval)
    {
        return ApiResponse::successResponse(__('success.data'), $approval);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ApprovalRequest $request, Approval $approval)
    {
        $data = $this->approvalService->update($approval, $request->only(['is_approved', 'is_approved']));
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
