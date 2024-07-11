<?php

namespace NadzorServera\Skijasi\Widgets;

use NadzorServera\Skijasi\Interfaces\WidgetInterface;
use NadzorServera\Skijasi\Module\Commerce\Models\Order;

class PermissionWidget implements WidgetInterface
{
    /**
     * Set permission for widget
     * set null to allow all role
     * multiple permission allowed, separate by comma.
     */
    public function getPermissions()
    {
        return 'browse_permissions';
    }

    public function run($params = null)
    {
        return [
            'label' => 'Ukupno narudžbi preko web-a',
            'icon' => 'lock',
            'value' => Order::count(),
            'prefix_value' => '',
            'delimiter' => '.',
        ];
    }
}
