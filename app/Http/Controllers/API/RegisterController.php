<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\ImageService;
use App\Services\UserService;

class RegisterController extends Controller
{
    //
    protected $userService, $imageService;

    public function __construct(UserService $userService, ImageService $imageService)
    {
        $this->userService = $userService;
        $this->imageService = $imageService;
    }

    /**
     * Register | ลงทะเบียนผ่านหน้าเว็บ
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->userService->register($request->all());
        $user->image = $this->imageService->uploadImage($request->image, User::IMAGE_FOLDER, User::class, $user->id);
        return ApiResponse::createdResponse(__('success.created'), $user);
    }
}
