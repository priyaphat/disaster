<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\User\FilterUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\UserApproval\ApprovalRequest;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

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
    public function index(FilterUserRequest $request)
    {
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->userService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Display a listing of the users for team | รายการผู้ใช้งาน.
     */
    public function selectable(FilterUserRequest $request)
    {
        $data = $this->userService->selectableUsers($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create a user | เพิ่มผู้ใช้งาน
     */
    public function store(StoreUserRequest $request)
    {
        //
        $data = $this->userService->create($request->all());
        return ApiResponse::createdResponse(__('success.user.created'), new UserResource($data));
    }

    /**
     * Show a user | เแสดงผู้ใช้งาน
     */
    public function show(User $user)
    {
        $data = $user->load(['prefix', 'department', 'position', 'area', 'approval', 'member', 'image', 'staff']);
        return ApiResponse::successResponse(__('success.data'), new UserResource($data));
    }

    /**
     * Update a user | แก้ไขผู้ใช้งาน
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
        $data = $this->userService->update($user, $request->all());
        return ApiResponse::successResponse(__('success.saved'), new UserResource($data->load('image')));
    }

    /**
     * Show a profile | เแสดงข้อมูลส่วนตัวของผู้ใช้งาน
     */
    public function profile()
    {
        //
        $user = Auth::user();
        $data = $user->load(['prefix', 'department', 'position', 'area', 'approval', 'member', 'image']);
        return ApiResponse::successResponse(__('success.data'), new ProfileResource($data));
    }

    /**
     * Update user's profile | แก้ไขข้อมูลส่วนตัว
     */
    public function updateProfile(UpdateUserRequest $request)
    {
        //
        $user = Auth::user();
        $data = $this->userService->update($user, $request->all());
        return ApiResponse::successResponse(__('success.saved'), new ProfileResource($data));
    }

    /**
     * Change password | เปลี่ยนรหัสผ่าน
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        //
        $user = Auth::user();
        $this->userService->update($user, $request->only('password'));
        return ApiResponse::successResponse(__('success.saved'));
    }

    /**
     * Delete a user | ลบผู้ใช้งาน
     */
    public function destroy(User $user)
    {
        //
        $this->userService->delete($user);
        return ApiResponse::successResponse(__('success.user.deleted'));
    }

    /**
     * Approve or Reject | อนุมัติหรือปฏิเสธผู้ใช้งาน
     */
    public function approve(ApprovalRequest $request)
    {
        //
        $message = $this->userService->approve($request->all());
        return ApiResponse::successResponse($message);
    }

}
