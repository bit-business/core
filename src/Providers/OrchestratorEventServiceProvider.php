<?php

namespace NadzorServera\Skijasi\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use NadzorServera\Skijasi\Events\CRUDDataChanged;
use NadzorServera\Skijasi\Listeners\SkijasiCRUDDataChanged;

class OrchestratorEventServiceProvider extends EventServiceProvider
{
    /** @var array */
    protected $listen = [
        CRUDDataChanged::class => [
            SkijasiCRUDDataChanged::class,
        ],
    ];
}
