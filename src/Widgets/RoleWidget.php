<?php

namespace NadzorServera\Skijasi\Widgets;

use NadzorServera\Skijasi\Interfaces\WidgetInterface;
use NadzorServera\Skijasi\Models\User;

class RoleWidget implements WidgetInterface
{
    /**
     * Set permission for widget
     * set null to allow all role
     * multiple permission allowed, separate by comma.
     */
    public function getPermissions()
    {
        return 'browse_roles';
    }

    public function run($params = null)
    {
        return [
            'label' => 'Ukupan broj svih korisnika',
            'icon' => 'accessibility',
            'value' => User::count(),
            'prefix_value' => '',
            'delimiter' => '.',
        ];
    }
}
