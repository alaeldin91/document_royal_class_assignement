<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            ['name' => 'General', 'enabled' => true, 'depends_on' => null],
            ['name' => 'Motors', 'enabled' => true, 'depends_on' => 'General'],
            ['name' => 'Jobs', 'enabled' => true, 'depends_on' => 'General'],
        ]);
    }
}

