<?php

namespace Database\Seeders;


use Database\Seeders\RolesAndPermissionsSeeder as SeedersRolesAndPermissionsSeeder;
use Illuminate\Database\Seeder;
use RolesAndPermissionsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SeedersRolesAndPermissionsSeeder::class);
        $this->call(UserSeeder::class);


    }
}
