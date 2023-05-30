<?php

namespace NadzorServera\Skijasi\Widgets;

use NadzorServera\Skijasi\Interfaces\WidgetInterface;
use NadzorServera\Skijasi\Models\User;

class UserWidget implements WidgetInterface
{
    /**
     * Set permission for widget
     * set null to allow all role
     * multiple permission allowed, separate by comma.
     */
    public function getPermissions()
    {
        return 'browse_users';
    }

    public function run($params = null)
    {
        return [
            'label' => 'User',
            'icon' => 'person',
            'value' => User::count(),
            'prefix_value' => '',
            'delimiter' => '.',
        ];
    }
}
