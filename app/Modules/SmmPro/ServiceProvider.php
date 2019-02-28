<?php

namespace App\Modules\SmmPro;

use App\Modules\SmmPro\Services\SmmPro;
use Illuminate\Support\ServiceProvider as BaseProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\SmmPro
 */
class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/Views', 'smmpro');
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SmmPro::class, function () {
            return new SmmPro;
        });

        $this->app->alias(SmmPro::class, 'smmpro');
    }
}
