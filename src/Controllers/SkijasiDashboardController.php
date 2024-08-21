<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\AuthenticatedUser;
use NadzorServera\Skijasi\Interfaces\WidgetInterface;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SkijasiDashboardController extends Controller
{
    public function index()
    {
         try {
            $widgets = config('skijasi.widgets');
            $data = [];
            foreach ($widgets as $widget) {
                $widget_class = new $widget();
                if ($widget_class instanceof WidgetInterface) {
                    $permissions = $widget_class->getPermissions();
                //    if (is_null($permissions)) {
                        $widget_data = $widget_class->run();
                        $data[] = $widget_data;
                  //  } else {
                     //   $allowed = AuthenticatedUser::isAllowedTo($permissions);
                    //    if ($allowed) {
                       //     $widget_data = $widget_class->run();
                         //   $data[] = $widget_data;
                       // }
                   // }
                }
            }

            return ApiResponse::success(collect($data)->toArray());
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function manifest()
    {
        $pwa_manifest = config('skijasi.manifest');

        return response($pwa_manifest, 200, [
            'Content-Type' => 'application/json',
        ]);
    }

  
    public function getCloudflareAnalytics()
    {
        try {
            $cloudflareToken = env('CLOUDFLARE_API_TOKEN');
            $zoneId = env('CLOUDFLARE_ZONE_ID');
    
            if (!$cloudflareToken || !$zoneId) {
                throw new Exception('Cloudflare API token or Zone ID not configured.');
            }
    
            $data24Hours = $this->fetchCloudflareData($cloudflareToken, $zoneId, '24h');
            $data3Days = $this->fetchCloudflareData($cloudflareToken, $zoneId, '3d');
    
            $processedData = [
                'pageViews24Hours' => $this->processCloudflareData($data24Hours, '24h'),
                'pageViews3Days' => $this->processCloudflareData($data3Days, '3d'),
            ];
    
            return ApiResponse::success($processedData);
        } catch (Exception $e) {
            \Log::error('Cloudflare Analytics Error:', ['message' => $e->getMessage()]);
            return ApiResponse::failed($e);
        }
    }

    private function fetchCloudflareData($token, $zoneId, $period)
    {
        $endTime = now()->toIso8601String();
        $startTime = now()->sub($period)->toIso8601String();
    
        $query = '';
        $limit = 0;
    
        if ($period === '24h') {
            $query = '
            query {
                viewer {
                    zones(filter: {zoneTag: "' . $zoneId . '"}) {
                        httpRequests1hGroups(
                            orderBy: [datetime_ASC],
                            limit: 24,
                            filter: {
                                datetime_geq: "' . $startTime . '",
                                datetime_lt: "' . $endTime . '"
                            }
                        ) {
                            dimensions {
                                datetime
                            }
                            sum {
                                pageViews
                            }
                            uniq {
                                uniques
                            }
                        }
                    }
                }
            }';
            $limit = 24;
        } else { // 3 days
            $query = '
            query {
                viewer {
                    zones(filter: {zoneTag: "' . $zoneId . '"}) {
                        httpRequests1dGroups(
                            orderBy: [date_ASC],
                            limit: 3,
                            filter: {
                                date_geq: "' . substr($startTime, 0, 10) . '",
                                date_lt: "' . substr($endTime, 0, 10) . '"
                            }
                        ) {
                            dimensions {
                                date
                            }
                            sum {
                                pageViews
                            }
                            uniq {
                                uniques
                            }
                        }
                    }
                }
            }';
            $limit = 3;
        }
    
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.cloudflare.com/client/v4/graphql', [
            'query' => $query
        ]);
    
        if ($response->successful()) {
            return $response->json();
        } else {
            \Log::error('Cloudflare API Error:', $response->json());
            throw new Exception('Failed to fetch data from Cloudflare: ' . $response->body());
        }
    }


private function processCloudflareData($data, $period)
{
    $processedData = [];
    $httpRequests = $period === '24h' 
        ? ($data['data']['viewer']['zones'][0]['httpRequests1hGroups'] ?? [])
        : ($data['data']['viewer']['zones'][0]['httpRequests1dGroups'] ?? []);

    foreach ($httpRequests as $request) {
        $datetime = $period === '24h'
            ? Carbon::parse($request['dimensions']['datetime'])
            : Carbon::parse($request['dimensions']['date']);
        
        $key = $period === '24h' ? $datetime->format('H:i') : $datetime->format('Y-m-d');

        $processedData[$key] = [
            'pageViews' => $request['sum']['pageViews'],
            'uniqueVisitors' => $request['uniq']['uniques']
        ];
    }

    return array_map(function($key, $value) {
        return [
            'date' => $key,
            'pageViews' => $value['pageViews'],
            'uniqueVisitors' => $value['uniqueVisitors']
        ];
    }, array_keys($processedData), $processedData);
}

}
