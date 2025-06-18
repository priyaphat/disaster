<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token as PassportToken;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{

    /**
     * Login function
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
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
        $user = User::where('username', $request->username)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $user->line_token = $request->userId;
            $user->update();
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
