<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class MigrateModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:modules {module?}'; // Accepts an optional 'module' argument

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate specified modules or all modules with dependency handling';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $module = $this->argument('module'); // Get the module argument

        if ($module) {
            $this->migrateModule($module);
        } else {
            // Migrate all modules based on their dependencies
            $modules = $this->getEnabledModulesWithDependencies();
            foreach ($modules as $module) {
                $this->migrateModule($module->name);
            }
        }
    }

    /**
     * Migrate a specific module.
     *
     * @param string $module
     * @return void
     */
    private function migrateModule(string $module)
    {
        $this->info("Migrating $module module...");

        $migrationPath = "app/modules/$module/migrations";
        if (is_dir(base_path($migrationPath))) {
            Artisan::call("migrate", ['--path' => $migrationPath]);
            $this->info("Migration for $module completed.");
        } else {
            $this->error("Migration path for $module not found.");
        }
    }

    /**
     * Get enabled modules sorted by dependencies.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getEnabledModulesWithDependencies()
    {
        return DB::table('modules')
            ->where('enabled', true)
            ->orderByRaw("FIELD(depends_on, NULL), depends_on")
            ->get();
    }
}
