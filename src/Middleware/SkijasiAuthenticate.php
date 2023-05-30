<?php

namespace NadzorServera\Skijasi\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use NadzorServera\Skijasi\Helpers\ApiResponse;

class SkijasiAuthenticate
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // $token_payload = Auth::payload();
            // $expires = $token_payload['exp'];
            // $now = time();
            // if ($expires <= $now) {
            //     return ApiResponse::unauthorized('expired authorization');
            // }

            return $next($request);
        }

        return ApiResponse::unauthorized();
    }
}
