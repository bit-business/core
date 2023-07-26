<?php

namespace NadzorServera\Skijasi\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use NadzorServera\Skijasi\Facades\Skijasi;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\AuthenticatedUser;

class SkijasiCheckPermissionsForCRUD
{
    public function handle($request, Closure $next, $slug, $action)
    {
        $data_type = Skijasi::model('DataType')::where('slug', $slug)->first();

        if ($data_type) {
            if ($data_type->generate_permissions == false) {
                return $next($request);
            } else {
                $continue = AuthenticatedUser::ignore($action.'_'.$data_type->name);
                if ($continue) {
                    return $next($request);
                } else {
                    if (Auth::check()) {
                        $continue = AuthenticatedUser::isAllowedTo($action.'_'.$data_type->name);
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

        return ApiResponse::forbidden();
    }
}
