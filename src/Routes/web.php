<?php

$prefix_file_manager_route = 'filemanager';
$middleware_file_manager = \config('lfm.middleware');

Route::group([
    'prefix' => '/',
    'namespace' => 'NadzorServera\Skijasi\Controllers',
    'as' => 'skijasi.',
], function () {
    Route::get('manifest.webmanifest', 'SkijasiDashboardController@manifest');
});

Route::group(['prefix' => $prefix_file_manager_route, 'middleware' => $middleware_file_manager], function () {
    if (class_exists("\UniSharp\LaravelFilemanager\Lfm")) {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    }
});

$admin_panel_route_prefix = \config('skijasi.admin_panel_route_prefix');
Route::get('/'.$admin_panel_route_prefix.'/{any?}', function () {
    return view('skijasi::admin-panel.index');
})->where('any', '.*');

