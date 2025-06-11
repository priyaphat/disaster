<?php

namespace App\Http\Controllers\API;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\SearchUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\UserApproval\ApprovalRequest;
use App\Http\Resources\UserCollection;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users | รายการผู้ใช้งาน.
     */
    public function index(SearchUserRequest $request)
    {
        $data = $this->userService->paginate($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create a user | เพิ่มผู้ใช้งาน
     */
    public function store(StoreUserRequest $request)
    {
        //
        $data = $this->userService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Show a user | เแสดงผู้ใช้งาน
     */
    public function show(User $user)
    {
        $data = $this->userService->show($user);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Update a user | แก้ไขผู้ใช้งาน
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
        $data = $this->userService->update($user, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Delete a user | ลบผู้ใช้งาน
     */
    public function destroy(User $user)
    {
        //
        $this->userService->delete($user);
        return ApiResponse::successResponse(__('success.deleted'));
    }

    /**
     * Approve or Reject | อนุมัติหรือปฏิเสธผู้ใช้งาน
     */
    public function approve(ApprovalRequest $request)
    {
        //
        $data = $this->userService->approve($request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
