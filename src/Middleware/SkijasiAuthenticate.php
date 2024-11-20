<?php

namespace NadzorServera\Skijasi\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use Carbon\Carbon;

class SkijasiAuthenticate
{

    /*
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


    zakomentirao ovo da dodam za ponocni logout
    */


    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Check for midnight logout for specific users
            $user = Auth::user();
            $targetUserIds = [1, 4, 5];
            
            if (in_array($user->id, $targetUserIds)) {
                $now = Carbon::now();
                $logoutTime = Carbon::now()->startOfDay()->addHours(1)->addMinutes(1); // 00:20
                
                // Only logout exactly at 00:20 (within a 1-minute window)
                if ($now->greaterThanOrEqualTo($logoutTime) && 
                    $now->lessThan($logoutTime->copy()->addMinute())) {
                    Auth::logout();
                    return ApiResponse::unauthorized('Vaša prijava je istekla. Molimo prijavite se opet');
                }
            }

            return $next($request);
        }

        return ApiResponse::unauthorized();
    }
}
