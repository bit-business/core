<?php

namespace NadzorServera\Skijasi\Controllers;

use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\Notification;

class SkijasiNotificationsController extends Controller
{
    public function getMessages()
    {
        try {
            $user = auth()->user();
           

            $fcm_messages = Notification::where('receiver_user_id', $user->id)->orderBy('created_at', 'desc')->get();
            foreach ($fcm_messages as $key => $value) {
                $value->sender_users;
            }

            return ApiResponse::success([
                'messages' => $fcm_messages->toArray(),
            ]);
        } catch (\Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function readMessage($id)
    {
        try {
            $fcm_messages = Notification::where('id', $id)->first();
            if (isset($fcm_messages)) {
                $fcm_messages->update([
                    'is_read' => true,
                ]);
            }

            return ApiResponse::success([
                'messages' => $fcm_messages->toArray(),
            ]);
        } catch (\Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function getCountUnreadMessage()
    {
        try {
            $user = auth()->user();
            $user_id = $user->id;
            $fcm_messages = Notification::where('receiver_user_id', $user_id)->where('is_read', true)->count();

            return ApiResponse::success([
                'count_unread_message' => $fcm_messages,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    

    public function clearAllNotifications()
{
    try {
        $user = auth()->user();
        $deleted = Notification::where('receiver_user_id', $user->id)->delete();
        
        if ($deleted) {
            return ApiResponse::success([
                'message' => 'All notifications have been deleted successfully.',
            ]);
        } else {
            return ApiResponse::success([
                'message' => 'No notifications to delete.',
            ]);
        }
    } catch (\Exception $e) {
        return ApiResponse::failed($e);
    }
}


// za slanje poruka stavio
    public function sendNotification(Request $request)
    {
      $notification = new Notification();
    
      // You can replace the token and data as needed
      $notification->send(
        ["AAAAt2ONPBo:APA91bEOK_iLTSNXpEoSqeV7_mV9Gv2xUgAdZL1_OltxabZ4M5H7WIvFZRlVgJG0f-97QMXf0RFv6xiRl1e1RPk_pOWgV_e6nKwmS_rqtPVp_iXE6YSpQIZ-8v7QF2G6ND5TVfbSPE3i"], 
        "Title", 
        "Message", 
        ["userId" => 1]
      );
    
      return response()->json(['message' => 'Notification sent']);
    }

}
