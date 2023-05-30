<?php

namespace NadzorServera\Skijasi\Facades;

use Illuminate\Support\Facades\Facade;

class Skijasi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'skijasi';
    }
}
