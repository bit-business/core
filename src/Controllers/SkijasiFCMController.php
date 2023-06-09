<?php

namespace NadzorServera\Skijasi\Controllers;

use Illuminate\Http\Request;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\FirebaseCloudMessages;

class SkijasiFCMController extends Controller
{
    public function saveTokenMessage(Request $request)
    {
        try {
            $request->validate([
                'token_get_message' => ['required'],
            ]);

            $user = \auth()->user();

            if (isset($user)) {
                $firebase_cloud_messages = FirebaseCloudMessages::where('user_id', $user->id)->first();
                if (isset($firebase_cloud_messages)) {
                    $firebase_cloud_messages->update([
                        'token_get_message' => $request->token_get_message,
                    ]);
                } else {
                    FirebaseCloudMessages::create([
                        'user_id' => $user->id,
                        'token_get_message' => $request->token_get_message,
                    ]);
                }
            }

            return ApiResponse::success([
                'token_message' => $request->token_get_message,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
