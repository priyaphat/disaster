<?php

namespace App\Http\Middleware;

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\Shelter;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthForSheltersList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('api')->check() && $request->has('status') && $request->status != Shelter::OPEN) {
            throw new AuthenticationException();
        }

         // Only the admin role can view close shelters

        if (Auth::guard('api')->check() && (!$request->has('status') || $request->status != Shelter::OPEN) && !$request->has('role')) {
            $user = Auth::guard('api')->user();
            $role = PermissionUtility::getRoleName($user);
            if($role !== Role::ADMIN){
                 throw new AuthorizationException();
            }
        }
        return $next($request);
    }
}
