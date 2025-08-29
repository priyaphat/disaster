<?php

namespace App\Http\Middleware;

use App\Models\Shelter;
use Closure;
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
        return $next($request);
    }
}
