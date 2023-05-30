<?php

namespace NadzorServera\Skijasi\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use NadzorServera\Skijasi\Facades\Skijasi;

class DataType extends Model
{
    protected $table = null;

    /**
     * Constructor for setting the table name dynamically.
     */
    public function __construct(array $attributes = [])
    {
        $prefix = config('skijasi.database.prefix');
        $this->table = $prefix.'data_types';
        parent::__construct($attributes);
    }

    protected $fillable = [
        'name',
        'slug',
        'display_name_singular',
        'display_name_plural',
        'icon',
        'model_name',
        'policy_name',
        'controller',
        'description',
        'generate_permissions',
        'server_side',
        'order_column',
        'order_display_column',
        'order_direction',
        'default_search_key',
        'scope',
        'details',
    ];

    public function dataRows()
    {
        return $this->hasMany(Skijasi::model('DataRow'))->orderBy('order', 'asc');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->dontSubmitEmptyLogs();
    }
}
