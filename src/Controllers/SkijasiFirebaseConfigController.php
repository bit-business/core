<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use Illuminate\Http\Request;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\FirebaseServices;

class SkijasiFirebaseConfigController extends Controller
{
    public function GetConfig()
    {
    }

    public function UpdateConfig(Request $request)
    {
        try {
            $request->validate([
                'apiKey'            => ['required'],
                'authDomain'        => ['required'],
                'projectId'         => ['required'],
                'storageBucket'     => ['required'],
                'messagingSenderId' => ['required'],
                'appId'             => ['required'],
                'measureId'         => ['required'],
                'serverKey'         => ['required'],
            ]);

            $firebaseService = FirebaseServices::first()->updateOrCreate([]);

            return ApiResponse::success([
                'firebase' => null,
            ]);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
