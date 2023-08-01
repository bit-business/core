<?php

return [
    'auth' => [
        'invalid_credentials'  => 'Neispravan email ili lozinka',
        'user_not_found'       => 'Korisnik nije pronađen',
        'wrong_old_password'   => 'Neispravna stara lozinka',
        'password_not_changes' => 'nova lozinka se mora razlikovati od stare lozinke',
    ],
    'crud' => [
        'table_not_found'                     => 'Table :table does not exists',
        'table_column_not_found'              => 'Invalid rows, Field :table_column does not exists',
        'table_column_not_have_default_value' => 'Invalid rows, Field :table_column has no default value, please tick the checkbox Add',
        'table_deleted_at_not_exists'         => 'Invalidate columns deleted_at, please created new columns delete_at in your table :table_name',
        'id_table_wrong'                      => 'Primary key should be named only "id"',
        'id_not_exist'                      => '"id" doesn\'t exist',
    ],
    'base64' => [
        'length_invalid'   => 'Base64 format is invalid',
        'header_invalid'   => 'Base64 header is invalid',
        'mimetype_invalid' => 'Base64 mimetype is invalid',
    ],
    'verification' => [
        'email_sended'               => 'Email poruka za potvrdu poslana je na vašu e-poštu',
        'invalid_verification_token' => 'Nevažeći token za provjeru',
        'verification_not_found'     => 'Potvrda nije pronađena',
        'time_wait_loading'          => 'Pričekajte dok se učitavanje ne završi',
    ],
    'database' => [
        'table_already_exists'      => 'Table :table already exists.',
        'table_name_already_exists' => 'Table name of :table already exists.',
        'migration_failed'          => 'Migration faield to migrate.',
        'migration_dropped'         => 'Table :table successfully dropped.',
        'migration_success'         => 'Migration successfully migrated.',
        'migration_deleted'         => 'Migration successfully deleted.',
        'alter_migration_created'   => 'Alter table :table successfully created and migrated.',
        'table_not_found'           => 'Table :table does not exists',
        'nothing_changed'           => 'Request was successful, but nothing changed.',
        'rollback_success'          => 'Rollback success.',
        'rollback_failed'           => 'Rollback failed.',
        'wrong_type_data'           => 'Your data type false',
    ],
];
