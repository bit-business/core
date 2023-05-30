<?php

namespace NadzorServera\Skijasi\Listeners;

use NadzorServera\Skijasi\SkijasiDeploymentOrchestrator;
use NadzorServera\Skijasi\Events\CRUDDataChanged;

class SkijasiCRUDDataChanged
{
    /** @var SkijasiDeploymentOrchestrator */
    private $deployment_orchestrator;

    /**
     * SkijasiCRUDDataChanged constructor.
     */
    public function __construct(SkijasiDeploymentOrchestrator $orchestrator)
    {
        $this->deployment_orchestrator = $orchestrator;
    }

    public function handle(CRUDDataChanged $crudDataChanged)
    {
        return $this->deployment_orchestrator->handle($crudDataChanged);
    }
}
