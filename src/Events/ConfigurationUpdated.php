<?php

namespace NadzorServera\Skijasi\Events;

use Illuminate\Queue\SerializesModels;
use NadzorServera\Skijasi\Models\Configuration;

class ConfigurationUpdated
{
    use SerializesModels;

    public $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }
}
