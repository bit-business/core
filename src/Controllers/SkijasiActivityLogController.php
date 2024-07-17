<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\User;

use Illuminate\Support\Facades\Log;

class SkijasiActivityLogController extends Controller
{
    public function browse(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $filter = '%'.$request->get('filter', '').'%';
            $order_field = $request->get('order_field');
            $order_direction = $request->get('order_direction');

            $activitylog_query = Activity::query()
                ->where('log_name', 'LIKE', $filter)
                ->orWhere('description', 'LIKE', $filter)
                ->orWhere('subject_id', 'LIKE', $filter)
                ->orWhere('subject_type', 'LIKE', $filter)
                ->orWhere('causer_id', 'LIKE', $filter)
                ->orWhere('causer_type', 'LIKE', $filter)
                ->orWhere('properties', 'LIKE', $filter);

            if ($order_field) {
                $order_field = Str::snake($order_field);
                $order_direction = $order_direction ? Str::snake($order_direction) : null;
                $activitylog_query = $activitylog_query->orderBy($order_field, $order_direction);
            } else {
                $activitylog_query = $activitylog_query->orderBy('created_at', 'desc');
            }

            $activitylog = $activitylog_query->paginate($limit);

            $data = json_decode(json_encode($activitylog));
            $data->activitylog = $activitylog->getCollection();

            foreach ($data->data as $index => $value) {
                $user = User::find($value->causer_id);
                $causer_name = '';
                if (isset($user)) {
                    $causer_name = $user->name;
                }
                $data->data[$index]->causer_name = $causer_name;
            }

            return ApiResponse::success(collect($data)->toArray());
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:activity_log,id',
            ]);

            $activitylog = Activity::find($request->id);

            $data['activitylog'] = $activitylog;
            $data['subject'] = $activitylog->subject;
            $data['causer'] = $activitylog->causer;
            $data['properties'] = $activitylog->properties->toArray();

            return ApiResponse::success($data);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }




    public function getstats(Request $request)
    {
        try {
            
            $start_date = $request->get('start_date', now()->subMonth());
            $end_date = $request->get('end_date', now());
    
       
    
            $logs = Activity::where('log_name', 'Prijava')
                ->orWhere('log_name', 'Odjava')
                ->whereBetween('created_at', [$start_date, $end_date])
                ->orderBy('causer_id')
                ->orderBy('created_at')
                ->get();
    
            \Log::info('Fetched logs count:', ['count' => $logs->count()]);
    
            $userStats = [];

            foreach ($logs as $index => $log) {
                $userId = $log->causer_id;
                
                if (!isset($userStats[$userId])) {
                    $userStats[$userId] = [
                        'total_duration' => 0,
                        'session_count' => 0,
                    ];
                }
    
                if ($log->log_name === 'Prijava') {
                    $userStats[$userId]['last_login'] = $log->created_at;
                    $userStats[$userId]['session_count']++;
                } elseif ($log->log_name === 'Odjava' && isset($userStats[$userId]['last_login'])) {
                    $duration = $log->created_at->diffInSeconds($userStats[$userId]['last_login']);
                    $userStats[$userId]['total_duration'] += $duration;
                    unset($userStats[$userId]['last_login']);
                }
            }
    
            // Convert total_duration to hours and calculate average
            foreach ($userStats as &$stats) {
                $stats['total_duration'] = round($stats['total_duration'] / 3600, 2); // Convert to hours
                $stats['average_duration'] = $stats['session_count'] > 0 
                    ? round($stats['total_duration'] / $stats['session_count'], 2) 
                    : 0;
            }
    
    
            return ApiResponse::success($userStats);
        } catch (Exception $e) {
          
            return ApiResponse::failed($e);
        }
    }

}
