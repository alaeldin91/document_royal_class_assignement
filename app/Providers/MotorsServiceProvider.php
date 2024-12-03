<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MotorsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $path = realpath(base_path('app/modules/Motors/Routes/routes.php'));
         $this->loadRoutesFrom($path);
     }
}