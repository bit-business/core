<?php

namespace NadzorServera\Skijasi\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use NadzorServera\Skijasi\ContentManager\FileGenerator;
use NadzorServera\Skijasi\Database\Schema\SchemaManager;
use NadzorServera\Skijasi\Facades\Skijasi;
use NadzorServera\Skijasi\Helpers\ApiResponse;
use NadzorServera\Skijasi\Models\Migration;

class SkijasiDatabaseController extends Controller
{
    /** @var FileGenerator */
    private $file_generator;

    private $file_name;

    public function __construct(FileGenerator $file_generator)
    {
        $this->file_generator = $file_generator;
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'table' => [
                    'required',
                    'unique:NadzorServera\Skijasi\Models\DataType,slug',
                    function ($attribute, $value, $fail) {
                        if (Schema::hasTable($value)) {
                            $fail(__('skijasi::validation.database.table_already_exists', ['table' => $value]));
                        }
                    },
                    Rule::notIn(Skijasi::getProtectedTables()),
                ],
                'rows' => 'required',
                'rows.*.field_name' => 'required|string|distinct',
                'rows.*.field_type' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                        $request_data = $request->rows;

                        $mysql_data_type = [
                            'tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint', 'decimal', 'float', 'double', 'bit', 'char', 'varchar', 'binary', 'varbinary', 'tinyblob', 'blob', 'mediumblob', 'longblob', 'tinytext', 'text', 'mediumtext', 'longtext', 'enum', 'set', 'date', 'time', 'datetime', 'timestamp', 'year', 'geometry', 'point', 'linestring', 'polygon', 'geometrycollection', 'multilinestring', 'multipoint', 'multipolygon', 'json', 'boolean',
                        ];
                        foreach ($request_data as $key => $value) {
                            if (! in_array($value['field_type'], $mysql_data_type)) {
                                $fail(__('skijasi::validation.database.wrong_type_data'));
                            }
                        }
                    },
                ],
            ]);

            $this->file_name = $this->file_generator->generateBDOMigrationFile($request->table, 'create', $request->rows, $request->relations);
            $exitCode = Artisan::call('migrate', [
                '--path' => 'database/migrations/skijasi/',
                '--force' => true,
            ]);

            switch ($exitCode) {
                case 0:
                    $msg = __('skijasi::validation.database.migration_success');

                    // activity('Database')
                    //     ->causedBy(auth()->user() ?? null)
                    //     ->withProperties(['attributes' => $request->all()])
                    //     ->event('created')
                    //     ->log('Add table '.$request->table.' has been created');

                    return ApiResponse::success($msg);

                    break;
                default:
                    if (isset($this->file_name)) {
                        $this->file_generator->deleteMigrationFiles($this->file_name);
                    }

                    return ApiResponse::failed(__('skijasi::validation.database.migration_failed'));
            }
        } catch (Exception $e) {
            if (isset($this->file_name)) {
                $this->file_generator->deleteMigrationFiles($this->file_name);
            }

            return ApiResponse::failed($e);
        }
    }

    public function read(Request $request)
    {
        try {
            $request->validate([
                'table' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (! Schema::hasTable($value)) {
                            $fail(__('skijasi::validation.database.table_not_found', ['table' => $value]));
                        }
                    },
                    Rule::notIn(Skijasi::getProtectedTables()),
                ],
            ]);

            $columns = SchemaManager::describeTable($request->table)->toArray();
            $columnsFK = SchemaManager::getDoctrineForeignKeys($request->table);
            $fKConstraints = [];
            foreach ($columnsFK as $columnFK) {
                $fKConstraints[$columnFK->getUnquotedLocalColumns()[0]] = [
                    'source_field' => $columnFK->getUnquotedLocalColumns()[0],
                    'target_table' => $columnFK->getForeignTableName(),
                    'target_field' => $columnFK->getForeignColumns()[0],
                    'on_delete' => strtolower($columnFK->getOption('onDelete')),
                    'on_update' => strtolower($columnFK->getOption('onUpdate')),
                ];
            }

            return ApiResponse::success(['columns' => $columns, 'columnsFK' => $fKConstraints]);
        } catch (Exception $e) {
            return APIResponse::failed($e);
        }
    }

    public function edit(Request $request)
    {
        try {
            $request->validate([
                'table.current_name' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (! Schema::hasTable($value)) {
                            $fail(__('skijasi::validation.database.table_not_found', ['table' => $value]));
                        }
                    },
                    Rule::notIn(Skijasi::getProtectedTables()),
                ],
                'table.modified_name' => [
                    'required',
                    Rule::notIn(Skijasi::getProtectedTables()),
                ],
                'fields.current_fields' => 'required|array',
                'fields.modified_fields' => 'required|array',
            ]);

            $data = $request->all();
            $fields = $data['fields'];
            $table = $data['table'];
            $relations = $data['relations'];

            if (count($fields['modified_fields']) > 0) {
                $this->file_name[] = $this->file_generator->generateBDOAlterMigrationFile($table, $fields, 'alter', $relations);
            }

            if ($table['current_name'] !== $table['modified_name']) {
                $this->file_name[] = $this->file_generator->generateBDOAlterMigrationFile($table, null, 'rename');
            }

            $exitCode = Artisan::call('migrate', [
                '--path' => 'database/migrations/skijasi/',
                '--force' => true,
            ]);

            switch ($exitCode) {
                case 0:
                    activity('Database')
                    ->causedBy(auth()->user() ?? null)
                    ->withProperties([
                        'old' => [$table['current_name'], $fields['current_fields'], $relations['current_relations']],
                        'new' => [$table['modified_name'], $fields['modified_fields'], $relations['modified_relations']],
                    ])
                    ->event('updated')
                    ->log('Edit table '.$table['current_name'].' has been updated');

                    return ApiResponse::success(__('skijasi::validation.database.alter_migration_created', ['table' => $table['modified_name']]));
                    break;

                default:
                    foreach ($this->file_name as $name) {
                        $this->file_generator->deleteMigrationFiles($name);
                    }

                    return ApiResponse::failed(__('skijasi::validation.database.migration_failed'));
            }
        } catch (Exception $e) {
            if (isset($this->file_name)) {
                foreach ($this->file_name as $name) {
                    $this->file_generator->deleteMigrationFiles($name);
                }
            }

            return ApiResponse::failed($e);
        }
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'table' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (! Schema::hasTable($value)) {
                            $fail(__('skijasi::validation.database.table_not_found', ['table' => $value]));
                        }
                    },
                    Rule::notIn(Skijasi::getProtectedTables()),
                ],
            ]);

            $columns = SchemaManager::describeTable($request->table)->all();

            $rows = array_map(function ($column) {
                return [
                    'field_name' => $column['name'],
                    'field_type' => $column['type'],
                    'field_null' => ! $column['null'],
                    'field_increment' => $column['autoincrement'],
                    'field_length' => $column['length'],
                    'field_default' => $column['default'] ? 'as_defined' : $column['default'],
                    'field_index' => $column['indexes'] == [] ? null : Str::lower(current($column['indexes'])['type']),
                    'field_attribute' => $column['unsigned'] ? 'unsigned' : null,
                    'as_defined' => $column['default'] ?? null,
                ];
            }, $columns);

            $this->file_name = $this->file_generator->generateBDOMigrationFile($request->table, 'drop', $rows);

            $exitCode = Artisan::call('migrate', [
                '--path' => 'database/migrations/skijasi/',
                '--force' => true,
            ]);

            switch ($exitCode) {
                case 0:
                    activity('Database')
                    ->causedBy(auth()->user() ?? null)
                    ->event('deleted')
                    ->log('Delete table '.$request->table.' has been deleted');

                    return ApiResponse::success(__('skijasi::validation.database.migration_dropped', ['table' => $request->table]));
                    break;
                default:
                    if (isset($this->file_name)) {
                        $this->file_generator->deleteMigrationFiles($this->file_name);
                    }

                    return ApiResponse::failed(__('skijasi::validation.database.migration_failed'));
            }
        } catch (Exception $e) {
            if (isset($this->file_name)) {
                $this->file_generator->deleteMigrationFiles($this->file_name);
            }

            return ApiResponse::failed($e);
        }
    }

    public function rollback(Request $request)
    {
        try {
            $request->validate([
                'step' => [
                    'required',
                    'numeric',
                ],
            ]);

            $exitCode = Artisan::call('migrate:rollback', [
                '--path' => 'database/migrations/skijasi/',
                '--step' => $request->step,
                '--force' => true,
            ]);

            switch ($exitCode) {
                case 0:
                    activity('Database')
                        ->causedBy(auth()->user() ?? null)
                        ->event('rollback')
                        ->log('Rollback table has been success');

                    return ApiResponse::success(__('skijasi::validation.database.rollback_success'));
                    break;
                default:
                    return ApiResponse::failed(__('skijasi::validation.database.rollback_failed'));
            }
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function browseMigration()
    {
        try {
            $migration = Migration::all();

            return ApiResponse::success($migration->toArray());
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function checkMigrateStatus()
    {
        try {
            $migration = Migration::all()->toArray();

            $skijasi_db_folder_path = database_path('migrations/skijasi/*.php');
            $skijasi_file = glob($skijasi_db_folder_path);
            $not_migrated_migration = [];
            $check = [];

            $file_name = [];
            foreach ($skijasi_file as $name) {
                $file_name[] = str_replace('.php', '', basename($name));
            }

            foreach ($migration as $key => $value) {
                $check[] = $value['migration'];
            }

            $not_migrated_migration = array_diff($file_name, $check);

            return ApiResponse::success(['data' => $not_migrated_migration, 'notMigrated' => ! empty($not_migrated_migration)]);
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function migrate()
    {
        try {
            $exitCode = Artisan::call('migrate', [
                '--path' => 'database/migrations/skijasi/',
                '--force' => true,
            ]);

            switch ($exitCode) {
                case 0:
                    return ApiResponse::success(__('skijasi::validation.database.migration_success'));
                    break;
                default:
                    return ApiResponse::failed(__('skijasi::validation.database.migration_failed'));
            }
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function deleteMigration(Request $request)
    {
        try {
            $request->validate([
                'file_name' => 'required|array',
            ]);

            foreach ($request->file_name as $key => $value) {
                $path = database_path('migrations/skijasi/').$value.'.php';
                if (file_exists($path)) {
                    unlink($path);
                }
            }

            $file_name = join(', ', $request->file_name);
            activity('Database')
                ->causedBy(auth()->user() ?? null)
                ->event('deleted')
                ->log('Migration '.$file_name.' has been deleted');

            return ApiResponse::success(__('skijasi::validation.database.migration_deleted'));
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }

    public function getDbmsFieldType()
    {
        try {
            return ApiResponse::success(Skijasi::getSkijasiDbmsFieldType());
        } catch (Exception $e) {
            return ApiResponse::failed($e);
        }
    }
}
