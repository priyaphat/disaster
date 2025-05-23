<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResetPasswordController extends Controller
{
     /**
     * Handle token in reset link.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkExpiredToken(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        $resetPassword = DB::table('password_reset_tokens')->where('email', '=', $email)->first();

        if (!$resetPassword) {
            throw new ModelNotFoundException();
        }

        if (!Hash::check($token, $resetPassword->token)) {
            throw new BadRequestHttpException(__('passwords.token'));
        }

        // Check if the token has expired
        $expiration = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire');
        $datetime = $resetPassword['created_at']->toDateTime();
        $isExpired = Carbon::parse($datetime)->addMinutes($expiration)->isPast();
        if ($isExpired) {
            throw new BadRequestHttpException(__('passwords.token_expired'));
        }
        
        return ApiResponse::successResponse(__('success.data'));
    }

    /**
     * Handle sending the reset password link email.
     *
     * @param \App\Http\Requests\Auth\ResetPasswordRequestt $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );
    
        return $status === Password::PasswordReset
            ? ApiResponse::successResponse(__($status))
            : ApiResponse::errorResponse(__($status), Response::HTTP_OK);
    }
}
