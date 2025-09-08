<?php

namespace App\Http\Middleware;

use App\Models\Shelter;
use Closure;
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
        if($shelter?->status != Shelter::OPEN && !Auth::guard('api')->check()){
            throw new AuthenticationException();
        }
        return $next($request);
    }
}
