<?php

namespace NadzorServera\Skijasi\Helpers\Redis;

use Illuminate\Support\Facades\Redis;
use NadzorServera\Skijasi\Models\Configuration;

class ConfigurationRedis
{
    // key redis
    public static $skijasi_configuration_redis_key = 'skijasi_configuration_redis_key';

    // save data configuration to redis
    public static function save()
    {
        $model_configurations = Configuration::all();
        try {
            Redis::set(self::$skijasi_configuration_redis_key, serialize($model_configurations));
        } catch (\Exception $th) {
            //throw $th;
        }
    }

    // load data from redis
    public static function get()
    {
        $result = [];
        try {
            $configuration_from_redis = Redis::get(self::$skijasi_configuration_redis_key);
            $result = unserialize($configuration_from_redis);
        } catch (\Exception $th) {
            $result = Configuration::all();
        }

        return $result;
    }
}
