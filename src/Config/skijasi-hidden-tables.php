<?php

return [
    // skijasi default table
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'data_rows',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'data_types',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'menus',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'menu_items',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'users',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'roles',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'permissions',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'configurations',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'role_permissions',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'user_roles',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'user_verifications',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'email_resets',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'notifications',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'firebase_cloud_messages',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'password_resets',
    env('SKIJASI_TABLE_PREFIX', 'skijasi_').'personal_access_tokens',

    // laravel default table
    'migrations',
    'activity_log',
    'failed_jobs',
    'personal_access_tokens',
    'users',
    'password_resets',
];
