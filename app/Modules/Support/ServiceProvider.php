<?php

namespace App\Modules\Support;

use App\Modules\Support\Services\SupportManager;
use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }

    public function register()
    {
        $this->app->singleton(SupportManager::class, function () {
            return new SupportManager;
        });

        $this->app->alias(SupportManager::class, 'help');
    }
}