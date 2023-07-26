<?php

namespace NadzorServera\Skijasi\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\AuthenticatedUser;

class SkijasiCheckPermissions
{
    public function handle($request, Closure $next, $permissions)
    {
        if ($permissions == null) {
            return $next($request);
        } else {
            $continue = AuthenticatedUser::ignore($permissions);
            if ($continue) {
                return $next($request);
            } else {
                if (Auth::check()) {
                    $continue = AuthenticatedUser::isAllowedTo($permissions);
                    if ($continue) {
                        return $next($request);
                    } else {
                        return ApiResponse::forbidden();
                    }
                }

                return ApiResponse::unauthorized();
            }
        }
    }
}
