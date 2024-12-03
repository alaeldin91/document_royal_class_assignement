<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GeneralServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $path = realpath(base_path('app/modules/General/Routes/routes.php'));
         $this->loadRoutesFrom($path);
     }
}

