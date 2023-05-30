<?php

namespace Database\Seeders\Skijasi;

use Illuminate\Database\Seeder;

class SkijasiSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(RolePermissionsSeeder::class);
        $this->call(MenusSeeder::class);
        $this->call(FixedMenuItemSeeder::class);
        $this->call(ConfigurationsSeeder::class);
    }
}
