<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Helpers\AuthenticatedUser;
use NadzorServera\Skijasi\Interfaces\WidgetInterface;

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
                    if (is_null($permissions)) {
                        $widget_data = $widget_class->run();
                        $data[] = $widget_data;
                    } else {
                        $allowed = AuthenticatedUser::isAllowedTo($permissions);
                        if ($allowed) {
                            $widget_data = $widget_class->run();
                            $data[] = $widget_data;
                        }
                    }
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
}
