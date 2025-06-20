<?php

namespace App\Http\Controllers\API;

use App\Enums\ApprovalStatus;
use App\Enums\RegistrationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Models\LineUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;


class LoginController extends Controller
{

    /**
     * Login function
     */
    public function login(LoginRequest $request)
    {
        $user = User::with('approval')->where('username', Str::lower($request->username))->first();

        if ($user && Hash::check($request->password, $user->password)) {

            if ($user->registration_type->value === RegistrationType::SELF->value) {
                if ($user->approval?->is_approved != ApprovalStatus::APPROVED->value) {
                    return ApiResponse::errorResponse(__('auth.waiting'), Response::HTTP_BAD_REQUEST);
                }
            }

            $token = $user->createToken('API Token')->accessToken;
            // Custom response for API
            $response = [
                'access_token' => $token
            ];
            return ApiResponse::customSuccessResponse('Login Successful', $response);
        }

        return ApiResponse::errorResponse(__('auth.failed'), Response::HTTP_BAD_REQUEST);
    }

    public function line(LoginRequest $request)
    {
        $user = User::where('username', Str::lower($request->username))->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $lineUser = LineUser::where('token_id', $request->userId)->first();

            if ($lineUser) {
                if ($lineUser->user_id) {
                    return ApiResponse::errorResponse('ready', Response::HTTP_BAD_REQUEST);
                }
                $lineUser->update(['user_id' => $user->id]);
            } else {
                LineUser::create([
                    'token_id' => $request->userId,
                    'user_id' => $user->id
                ]);
            }
            (new LineController)->user_login($request->userId, __('line.welcome'));
            return ApiResponse::customSuccessResponse('Login Successful');
        }
        return ApiResponse::errorResponse(__('auth.failed'), Response::HTTP_BAD_REQUEST);
    }

    /**
     * Logout function
     */
    public function logout(Request $request)
    {
        $user = Auth::user()->token();
        $user->revoke();
        return ApiResponse::successResponse('Logout Successful');
    }
}
