<?php

return [
    'db_name' => env('DB_DATABASE'),
    'admin_panel_route_prefix' => env('MIX_ADMIN_PANEL_ROUTE_PREFIX', 'skijasi-dashboard'),
    'default_menu' => env('MIX_DEFAULT_MENU', 'general'),
    'api_route_prefix' => env('MIX_API_ROUTE_PREFIX', 'skijasi-api'),
    'secret_login_prefix' => env('MIX_SKIJASI_SECRET_LOGIN_PREFIX'),
    'skijasi_maintenance' => env('MIX_SKIJASI_MAINTENANCE'),
    'license_key' => env('SKIJASI_LICENSE_KEY'),
    'database' => [
        'prefix' => env('SKIJASI_TABLE_PREFIX', 'skijasi_'),
    ],
    'storage' => [
        'disk' => env('FILESYSTEM_DRIVER', 'public'),
    ],
    'configuration_groups' => [
        ['value' => 'adminPanel', 'label' => 'Admin Panel'],
    ],
    'widgets' => [
        'NadzorServera\\Skijasi\\Widgets\\UserWidget',
        'NadzorServera\\Skijasi\\Widgets\\RoleWidget',
        'NadzorServera\\Skijasi\\Widgets\\PermissionWidget',
    ],
    'whitelist' => [
        'web' => [],
        'skijasi' => [
            '/maintenance',
            '/'.env('MIX_SKIJASI_SECRET_LOGIN_PREFIX'),
        ],
        'api' => [
            '/v1/configurations/applyable',
            '/v1/maintenance',
            '/v1/auth/'.env('MIX_SKIJASI_SECRET_LOGIN_PREFIX'),
            '/v1/file/*',
        ],
    ],
    'manifest' => [
        'name' => 'Skijasi',
        'short_name' => 'Skijasi',
        'description' => 'Skijasi Progressive Web App ',
        'icons' => [
            [
                'src' => '/storage/photos/shares/logo-144px.png',
                'sizes' => '144x144',
                'type' => 'image/png',
            ],
            [
                'src' => '/storage/photos/shares/logo-192px.png',
                'sizes' => '192x192',
                'type' => 'image/png',
            ],
            [
                'src' => '/storage/photos/shares/logo-512px.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ],
        ],
        'start_url' => env('MIX_ADMIN_PANEL_ROUTE_PREFIX', 'skijasi-dashboard'),
        'display' => 'standalone',
        'theme_color' => '#06bbd3',
        'background_color' => '#06bbd3',
    ],
];
