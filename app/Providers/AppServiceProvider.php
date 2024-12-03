<?php

namespace App\Providers;

use App\Repositories\DocumentRepository;
use App\Repositories\DocumentRepositoryImp;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(DocumentRepository::class, DocumentRepositoryImp::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
