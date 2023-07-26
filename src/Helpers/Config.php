<?php

namespace NadzorServera\Skijasi\Helpers;

use NadzorServera\Skijasi\Models\Configuration;

class Config
{
    public static function get($key)
    {
        $config = Configuration::where('key', $key)->first();
        if ($config) {
            return $config->value;
        } else {
            return null;
        }
    }
}
