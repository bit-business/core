<?php

use Illuminate\Support\Str;
use NadzorServera\Skijasi\Facades\Skijasi;
use NadzorServera\Skijasi\Middleware\ApiRequest;
use NadzorServera\Skijasi\Middleware\SkijasiAuthenticate;
use NadzorServera\Skijasi\Middleware\SkijasiCheckPermissions;
use NadzorServera\Skijasi\Middleware\SkijasiCheckPermissionsForCRUD;

$api_route_prefix = \config('skijasi.api_route_prefix');
Route::group(['prefix' => $api_route_prefix, 'namespace' => 'NadzorServera\Skijasi\Controllers', 'as' => 'skijasi.', 'middleware' => [ApiRequest::class]], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', 'SkijasiDashboardController@index')->middleware(SkijasiAuthenticate::class);
        });


        

        Route::group(['prefix' => 'data'], function () {
            Route::get('/components', 'SkijasiDataController@getComponents');
            Route::get('/filter-operators', 'SkijasiDataController@getFilterOperators');
            Route::get('/table-relations', 'SkijasiDataController@getSupportedTableRelations');
            Route::get('/configuration-groups', 'SkijasiDataController@getConfigurationGroups');
        });

        Route::group(['prefix' => 'activitylogs'], function () {
            Route::get('/', 'SkijasiActivityLogController@browse')->middleware(SkijasiCheckPermissions::class.':browse_activitylogs');
            Route::get('/read', 'SkijasiActivityLogController@read')->middleware(SkijasiCheckPermissions::class.':read_activitylogs');
        });

        Route::group(['prefix' => 'auth'], function () {
            Route::post('/login', 'SkijasiAuthController@login');
            Route::post('/logout', 'SkijasiAuthController@logout');
            Route::post('/register', 'SkijasiAuthController@register');
            Route::post('/forgot-password', 'SkijasiAuthController@forgetPassword');
            Route::post('/forgot-password-verify', 'SkijasiAuthController@validateTokenForgetPassword');
            Route::post('/reset-password', 'SkijasiAuthController@resetPassword');
            Route::post('/refresh-token', 'SkijasiAuthController@refreshToken');
            Route::post('/verify', 'SkijasiAuthController@verify');
            Route::post('/re-request-verification', 'SkijasiAuthController@reRequestVerification');
            Route::post('/'.env('MIX_SKIJASI_SECRET_LOGIN_PREFIX'), 'SkijasiAuthController@secretLogin');

            Route::post('/send-contact-form', 'SkijasiAuthController@sendContactForm')->middleware('throttle:2,1');

        });

        Route::group(['prefix' => 'auth/user'], function () {
            Route::get('/', 'SkijasiAuthController@getAuthenticatedUser');
            Route::put('/change-password', 'SkijasiAuthController@changePassword');
            Route::put('/profile', 'SkijasiAuthController@updateProfile');
            Route::put('/email', 'SkijasiAuthController@updateEmail');
            Route::post('/verify-email', 'SkijasiAuthController@verifyEmail');



           

        });

        Route::group(['prefix' => 'file'], function () {
            Route::get('/view', 'SkijasiFileController@viewFile');
            Route::get('/download', 'SkijasiFileController@downloadFile');
            Route::post('/upload', 'SkijasiFileController@uploadFile')->middleware(SkijasiCheckPermissions::class.':upload_file');
            Route::delete('/delete', 'SkijasiFileController@deleteFile');
            Route::get('/browse/lfm', 'SkijasiFileController@browseFileUsingLfm');
            Route::post('/upload/lfm', 'SkijasiFileController@uploadFileUsingLfm');
            Route::get('/delete/lfm', 'SkijasiFileController@deleteFileUsingLfm');
            Route::get('/mimetypes', 'SkijasiFileController@availableMimetype');


            Route::post('/upload/custom', 'SkijasiFileController@customuploadfile');
            Route::post('/upload/customvijesti', 'SkijasiFileController@customuploadfilevijesti');
            Route::get('/getfolders', 'SkijasiFileController@getFolders');

            Route::get('/get-images-from-slike', 'SkijasiFileController@getImagesFromSlike');

        });

        Route::group(['prefix' => 'configurations'], function () {
            Route::get('/applyable', 'SkijasiConfigurationsController@applyable');
            Route::get('/maintenance', 'SkijasiConfigurationsController@isMaintenance');

            Route::get('/', 'SkijasiConfigurationsController@browse')->middleware(SkijasiCheckPermissions::class.':browse_configurations');
            Route::get('/read', 'SkijasiConfigurationsController@read')->middleware(SkijasiCheckPermissions::class.':read_configurations');
            Route::get('/fetch', 'SkijasiConfigurationsController@fetch');
            Route::get('/fetch-multiple', 'SkijasiConfigurationsController@fetchMultiple');
            Route::put('/edit', 'SkijasiConfigurationsController@edit')->middleware(SkijasiCheckPermissions::class.':edit_configurations');
            Route::put('/edit-multiple', 'SkijasiConfigurationsController@editMultiple')->middleware(SkijasiCheckPermissions::class.':edit_configurations');
            Route::post('/add', 'SkijasiConfigurationsController@add')->middleware(SkijasiCheckPermissions::class.':add_configurations');
            Route::delete('/delete', 'SkijasiConfigurationsController@delete')->middleware(SkijasiCheckPermissions::class.':delete_configurations');
        });

        Route::group(['prefix' => 'menus'], function () {
            Route::get('/', 'SkijasiMenuController@browseMenu')->middleware(SkijasiCheckPermissions::class.':browse_menus');
            Route::get('/read', 'SkijasiMenuController@readMenu')->middleware(SkijasiCheckPermissions::class.':read_menus');
            Route::put('/edit', 'SkijasiMenuController@editMenu')->middleware(SkijasiCheckPermissions::class.':edit_menus');
            Route::post('/add', 'SkijasiMenuController@addMenu')->middleware(SkijasiCheckPermissions::class.':add_menus');
            Route::delete('/delete', 'SkijasiMenuController@deleteMenu')->middleware(SkijasiCheckPermissions::class.':delete_menus');
            Route::put('/arrange-items', 'SkijasiMenuController@editMenuItemsOrder')->middleware(SkijasiCheckPermissions::class.':edit_menus');

            Route::get('/item', 'SkijasiMenuController@browseMenuItem')->middleware(SkijasiCheckPermissions::class.':browse_menu_items');
            Route::get('/item/read', 'SkijasiMenuController@readMenuItem')->middleware(SkijasiCheckPermissions::class.':read_menu_items');
            Route::put('/item/edit', 'SkijasiMenuController@editMenuItem')->middleware(SkijasiCheckPermissions::class.':edit_menu_items');
            Route::put('/item/edit-order', 'SkijasiMenuController@editMenuItemOrder')->middleware(SkijasiCheckPermissions::class.':edit_menu_items');
            Route::post('/item/add', 'SkijasiMenuController@addMenuItem')->middleware(SkijasiCheckPermissions::class.':add_menu_items');
            Route::delete('/item/delete', 'SkijasiMenuController@deleteMenuItem')->middleware(SkijasiCheckPermissions::class.':delete_menu_items');
            Route::get('/item/permissions', 'SkijasiMenuController@getMenuItemPermissions')->middleware(SkijasiCheckPermissions::class.':edit_menu_items');
            Route::put('/item/permissions', 'SkijasiMenuController@setMenuItemPermissions')->middleware(SkijasiCheckPermissions::class.':edit_menu_items');

            Route::get('/item-by-key', 'SkijasiMenuController@browseMenuItemByKey');
            Route::get('/item-by-keys', 'SkijasiMenuController@browseMenuItemByKeys');

            Route::put('/menu-options', 'SkijasiMenuController@menuOptions');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'SkijasiUserController@browse')->middleware(SkijasiCheckPermissions::class.':browse_users');
            Route::get('/read', 'SkijasiUserController@read')->middleware(SkijasiCheckPermissions::class.':read_users');
            Route::put('/edit', 'SkijasiUserController@edit')->middleware(SkijasiCheckPermissions::class.':edit_users');
            Route::post('/add', 'SkijasiUserController@add')->middleware(SkijasiCheckPermissions::class.':add_users');
            Route::delete('/delete', 'SkijasiUserController@delete')->middleware(SkijasiCheckPermissions::class.':delete_users');
            Route::delete('/delete-multiple', 'SkijasiUserController@deleteMultiple')->middleware(SkijasiCheckPermissions::class.':delete_users');

            Route::get('/unapproved-avatars', 'SkijasiUserController@unapprovedAvatars');
            Route::put('/approve-avatar', 'SkijasiUserController@approveAvatar');
            Route::put('/decline-avatar', 'SkijasiUserController@declineAvatar');

            Route::get('/zadnjiidmember', 'SkijasiUserController@zadnjiidmember');

            Route::get('/browsenasiclanovi', 'SkijasiUserController@browsenasiclanovi');
            Route::get('/readmojstatus', 'SkijasiUserController@readmojstatus');


        });

        Route::group(['prefix' => 'permissions'], function () {
            Route::get('/', 'SkijasiPermissionController@browse')->middleware(SkijasiCheckPermissions::class.':browse_permissions');
            Route::get('/read', 'SkijasiPermissionController@read')->middleware(SkijasiCheckPermissions::class.':read_permissions');
            Route::put('/edit', 'SkijasiPermissionController@edit')->middleware(SkijasiCheckPermissions::class.':edit_permissions');
            Route::post('/add', 'SkijasiPermissionController@add')->middleware(SkijasiCheckPermissions::class.':add_permissions');
            Route::delete('/delete', 'SkijasiPermissionController@delete')->middleware(SkijasiCheckPermissions::class.':delete_permissions');
            Route::delete('/delete-multiple', 'SkijasiPermissionController@deleteMultiple')->middleware(SkijasiCheckPermissions::class.':delete_permissions');
        });

        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', 'SkijasiRoleController@browse')->middleware(SkijasiCheckPermissions::class.':browse_roles');
            Route::get('/read', 'SkijasiRoleController@read')->middleware(SkijasiCheckPermissions::class.':read_roles');
            Route::put('/edit', 'SkijasiRoleController@edit')->middleware(SkijasiCheckPermissions::class.':edit_roles');
            Route::post('/add', 'SkijasiRoleController@add')->middleware(SkijasiCheckPermissions::class.':add_roles');
            Route::delete('/delete', 'SkijasiRoleController@delete')->middleware(SkijasiCheckPermissions::class.':delete_roles');
            Route::delete('/delete-multiple', 'SkijasiRoleController@deleteMultiple')->middleware(SkijasiCheckPermissions::class.':delete_roles');
        });

        Route::group(['prefix' => 'user-roles'], function () {
            Route::get('/', 'SkijasiUserRoleController@browseByUser')->middleware(SkijasiCheckPermissions::class.':browse_user_role');
            Route::get('/all', 'SkijasiUserRoleController@browse')->middleware(SkijasiCheckPermissions::class.':browse_user_role');
            Route::post('/add-edit', 'SkijasiUserRoleController@addOrEdit')->middleware(SkijasiCheckPermissions::class.':add_or_edit_user_role');
            Route::get('/all-role', 'SkijasiUserRoleController@browseAllRole')->middleware(SkijasiCheckPermissions::class.':browse_user_role');
        });

        Route::group(['prefix' => 'role-permissions'], function () {
            Route::get('/', 'SkijasiRolePermissionController@browseByRole')->middleware(SkijasiCheckPermissions::class.':browse_role_permission');
            Route::get('/all', 'SkijasiRolePermissionController@browse')->middleware(SkijasiCheckPermissions::class.':browse_role_permission');
            Route::post('/add-edit', 'SkijasiRolePermissionController@addOrEdit')->middleware(SkijasiCheckPermissions::class.':add_or_edit_role_permission');
            Route::get('/all-permission', 'SkijasiRolePermissionController@browseAllPermission')->middleware(SkijasiCheckPermissions::class.':browse_role_permission');
        });

        Route::group(['prefix' => 'crud'], function () {
            Route::get('/', 'SkijasiCRUDController@browse')->middleware(SkijasiCheckPermissions::class.':browse_crud_data');
            Route::get('/read', 'SkijasiCRUDController@read')->middleware(SkijasiCheckPermissions::class.':read_crud_data');
            Route::put('/edit', 'SkijasiCRUDController@edit')->middleware(SkijasiCheckPermissions::class.':edit_crud_data');
            Route::post('/add', 'SkijasiCRUDController@add')->middleware(SkijasiCheckPermissions::class.':add_crud_data');
            Route::delete('/delete', 'SkijasiCRUDController@delete')->middleware(SkijasiCheckPermissions::class.':delete_crud_data');
            Route::get('/read-by-slug', 'SkijasiCRUDController@readBySlug')->middleware(SkijasiCheckPermissions::class.':read_crud_data');
            Route::get('/maintenance', 'SkijasiCRUDController@setMaintenanceState')->middleware(SkijasiCheckPermissions::class.':maintenance_crud_data');
      


        });

        Route::group(['prefix' => 'table'], function () {
            Route::get('/data-type', 'SkijasiTableController@getDataType')->middleware(SkijasiAuthenticate::class);
            Route::get('/', 'SkijasiTableController@browse')->middleware(SkijasiAuthenticate::class);
            Route::get('/read', 'SkijasiTableController@read')->middleware(SkijasiAuthenticate::class);
            Route::get('/data', 'SkijasiTableController@getDataByTable')->middleware(SkijasiAuthenticate::class);
            Route::get('/generate-crud', 'SkijasiTableController@generateCRUD')->middleware(SkijasiAuthenticate::class);
            Route::get('/relation-data-by-slug', 'SkijasiTableController@getRelationDataBySlug')->middleware(SkijasiAuthenticate::class);
        });

        Route::group(['prefix' => 'maintenance'], function () {
            Route::post('/', 'SkijasiMaintenanceController@isMaintenance');
        });

        Route::group(['prefix' => 'entities'], function () {
            try {
                foreach (Skijasi::model('DataType')::all() as $data_type) {
                    $crud_data_controller = $data_type->controller
                        ? Str::start($data_type->controller, '\\')
                        : 'SkijasiBaseController';
                    Route::get($data_type->slug, $crud_data_controller.'@browse')
                        ->name($data_type->slug.'.browse')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',browse');

                    Route::get($data_type->slug.'/read', $crud_data_controller.'@read')
                        ->name($data_type->slug.'.read')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',read');

                        Route::get($data_type->slug.'/citanje', $crud_data_controller.'@citanje')
                        ->name($data_type->slug.'.citanje')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',read');

                        
                        Route::get($data_type->slug.'/citanjenasiclanovi', $crud_data_controller.'@citanje')
                        ->name($data_type->slug.'.citanjenasiclanovi');

                        Route::get($data_type->slug.'/allnasiclanovi', $crud_data_controller.'@all')
                        ->name($data_type->slug.'.allnasiclanovi');
                    


                        Route::get($data_type->slug.'/citanjeispiti', $crud_data_controller.'@citanjeispiti')
                        ->name($data_type->slug.'.citanjeispiti')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',read');

                    Route::put($data_type->slug.'/edit', $crud_data_controller.'@edit')
                        ->name($data_type->slug.'.edit')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',edit');

                    Route::post($data_type->slug.'/add', $crud_data_controller.'@add')
                        ->name($data_type->slug.'.add')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',add');

                    Route::delete($data_type->slug.'/delete', $crud_data_controller.'@delete')
                        ->name($data_type->slug.'.delete')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',delete');

                    Route::delete($data_type->slug.'/restore', $crud_data_controller.'@restore')
                        ->name($data_type->slug.'.restore')->middleware(SkijasiAuthenticate::class);

                    Route::delete($data_type->slug.'/delete-multiple', $crud_data_controller.'@deleteMultiple')
                        ->name($data_type->slug.'.delete-multiple')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',delete');

                    Route::delete($data_type->slug.'/restore-multiple', $crud_data_controller.'@restoreMultiple')
                        ->name($data_type->slug.'.restore-multiple')->middleware(SkijasiAuthenticate::class);

                    Route::put($data_type->slug.'/sort', $crud_data_controller.'@sort')
                        ->name($data_type->slug.'.sort')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',edit');

                    Route::get($data_type->slug.'/all', $crud_data_controller.'@all')
                        ->name($data_type->slug.'.all')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',edit');



                        Route::get($data_type->slug.'/generatepdff', $crud_data_controller.'@generatepdff')
                        ->name($data_type->slug.'.generatepdff');
                        Route::get($data_type->slug.'/generatepdffprint', $crud_data_controller.'@generatepdffprint')
                        ->name($data_type->slug.'.generatepdffprint');
                        Route::get($data_type->slug.'/generatepdffid', $crud_data_controller.'@generatepdffid')
                        ->name($data_type->slug.'.generatepdffid');

                        Route::get($data_type->slug.'/generatepdffpotvrdaisia', $crud_data_controller.'@generatepdffpotvrdaisia')
                        ->name($data_type->slug.'.generatepdffpotvrdaisia');
                        Route::get($data_type->slug.'/generatepdffpotvrdaivsi', $crud_data_controller.'@generatepdffpotvrdaivsi')
                        ->name($data_type->slug.'.generatepdffpotvrdaivsi');


                        Route::get($data_type->slug.'/zadnjimaticni', $crud_data_controller.'@zadnjimaticni')
                        ->name($data_type->slug.'.zadnjimaticni');

                        Route::post($data_type->slug.'/generateisiagodinu', $crud_data_controller.'@generateisiagodinu')
                        ->name($data_type->slug.'.generateisiagodinu');



                    Route::post($data_type->slug.'/maintenance', $crud_data_controller.'@setMaintenanceState')
                        ->name($data_type->slug.'.maintenance')
                        ->middleware(SkijasiCheckPermissionsForCRUD::class.':'.$data_type->slug.',maintenance');

                }
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Custom routes hasn't been configured because: ".$e->getMessage(), 1);
            } catch (\Exception $e) {
                // do nothing, might just be because table not yet migrated.
            }
        });

        Route::group(['prefix' => 'database'], function () {
            Route::get('/', 'SkijasiDatabaseController@browse')->middleware(SkijasiCheckPermissions::class.':browse_database');
            Route::get('/read', 'SkijasiDatabaseController@read')->middleware(SkijasiCheckPermissions::class.':read_database');
            Route::put('/edit', 'SkijasiDatabaseController@edit')->middleware(SkijasiCheckPermissions::class.':edit_database');
            Route::post('/add', 'SkijasiDatabaseController@add')->middleware(SkijasiCheckPermissions::class.':add_database');
            Route::delete('/delete', 'SkijasiDatabaseController@delete')->middleware(SkijasiCheckPermissions::class.':delete_database');
            Route::post('/rollback', 'SkijasiDatabaseController@rollback')->middleware(SkijasiCheckPermissions::class.':rollback_database');
            Route::get('/migration/browse', 'SkijasiDatabaseController@browseMigration')->middleware(SkijasiCheckPermissions::class.':browse_migration');
            Route::get('/migration/status', 'SkijasiDatabaseController@checkMigrateStatus');
            Route::get('/migration/migrate', 'SkijasiDatabaseController@migrate')->middleware(SkijasiCheckPermissions::class.':migrate_database');
            Route::post('/migration/delete', 'SkijasiDatabaseController@deleteMigration')->middleware(SkijasiCheckPermissions::class.':delete_migration');
            Route::get('/type', 'SkijasiDatabaseController@getDbmsFieldType')->middleware([SkijasiCheckPermissions::class.':add_database', SkijasiCheckPermissions::class.':edit_database']);
        });

        Route::group(['prefix' => 'firebase', 'middleware' => 'auth'], function () {
            Route::group(['prefix' => 'cloud_messages'], function () {
                Route::put('/save-token-messages', 'SkijasiFCMController@saveTokenMessage');

                
                Route::post('/send-firebase-message', 'SkijasiFCMController@sendNotification');

            });
            Route::group(['prefix' => 'messages', 'middleware' => 'auth'], function () {
                Route::get('/', 'SkijasiNotificationsController@getMessages');
                Route::put('/{id}', 'SkijasiNotificationsController@readMessage');
                Route::get('/count-unread', 'SkijasiNotificationsController@getCountUnreadMessage');

                


            });
        });
    });
});
