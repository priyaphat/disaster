<?php

namespace App\Http\Middleware;

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\Shelter;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthForShelters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shelter = $request->route('shelter');
        if ($shelter?->status != Shelter::OPEN && !Auth::guard('api')->check()) {
            throw new AuthenticationException();
        }


        // Only the admin role can view close shelters
        if (Auth::guard('api')->check() && $shelter?->status != Shelter::OPEN) {
            $user = Auth::guard('api')->user();
            $role = PermissionUtility::getRoleName($user);

            if($role === Role::TEAM_LEADER || $role === Role::TEAM_MEMBER){
                $allowedShelter = PermissionUtility::getShelterByTeam(Auth::guard('api')->user()->member?->team_id);
                if(!$allowedShelter->search($shelter->id)){
                    throw new AuthorizationException();
                }
            }
            
            if($role === Role::STAFF && $user->staff?->shelter_id !== $shelter->id){
                throw new AuthorizationException();
            }
            
        }
        return $next($request);
    }
}
