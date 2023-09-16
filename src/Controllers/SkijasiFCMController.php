<?php

namespace NadzorServera\Skijasi\Controllers;

use Illuminate\Http\Request;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\FirebaseCloudMessages;
use NadzorServera\Skijasi\Helpers\Firebase\FCMNotification;


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

       // za slanje poruka stavio
       public function sendNotification(Request $request)
       {
         $notification = new FCMNotification();
       
         // You can replace the token and data as needed
         $notification->send(
           ["AAAAt2ONPBo:APA91bFwCadksbJU8pPd98OKPYwntUnw8V8twTS1DO2U2yKgkBcqbxShxOplHfXk63-JtsJ3j4h1-vTbs5hZ-o9UeHlA-X7WBx3ubRtCKRCWwO6SxMzziBPlm1V3IQgokhbhslKJpZf0"], 
           "Title", 
           "Message", 
           ["userId" => 1]
         );
       
         return response()->json(['message' => 'Notification sent']);
       }
}
