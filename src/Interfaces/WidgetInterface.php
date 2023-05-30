<?php

namespace NadzorServera\Skijasi\Interfaces;

/**
 * @author Sulfano Agus Fikri
 */
interface WidgetInterface
{
    public function getPermissions();

    public function run($params = null);
}
