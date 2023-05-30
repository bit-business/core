<?php

namespace NadzorServera\Skijasi\Widgets;

use NadzorServera\Skijasi\Interfaces\WidgetInterface;
use NadzorServera\Skijasi\Models\Role;

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
            'label' => 'Role',
            'icon' => 'accessibility',
            'value' => Role::count(),
            'prefix_value' => '',
            'delimiter' => '.',
        ];
    }
}
