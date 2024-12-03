<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class JobServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $path = realpath(base_path('app/modules/Jobs/Routes/routes.php'));
         $this->loadRoutesFrom($path);
     }
}