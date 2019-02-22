<?php

namespace Betalectic\Permiso;

use Illuminate\Support\ServiceProvider;

class PermisoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
    }
}
