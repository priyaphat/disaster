<?php

namespace App\Http\Controllers\API;

use App\Enums\ApprovalStatus;
use App\Http\Controllers\Controller;
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
    public function index(Request $request, ?string $action = null)
    {
        $data = $this->userService->paginate($request->all(), $action);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
        $data = $this->userService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $data = $this->userService->show($user);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
        $data = $this->userService->update($user, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
        $this->userService->delete($user);
        return ApiResponse::successResponse(__('success.deleted'));
    }

    /**
     * Update the approval in database.
     */
    public function approve(ApprovalRequest $request)
    {
        //
        $data = $this->userService->approve($request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
