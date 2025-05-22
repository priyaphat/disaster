<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Services\UserService;

class RegisterController extends Controller
{
    //
    protected $userService, $approvalService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->userService->register($request->all());
        return ApiResponse::createdResponse(__('success.created'), $user);
    }
}
