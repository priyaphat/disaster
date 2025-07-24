<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\UserService;

class RegisterController extends Controller
{
    //
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register | ลงทะเบียนผ่านหน้าเว็บ
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->userService->register($request->all());
        return ApiResponse::createdResponse(__('success.created'), new UserResource($user));
    }
}
