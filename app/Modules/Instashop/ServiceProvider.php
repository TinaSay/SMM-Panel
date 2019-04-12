<?php

namespace App\Modules\Instashop;

use App\Modules\Instashop\Services\InstashopService;
use Illuminate\Support\ServiceProvider as BaseProvider;

/**
 * Class ServiceProvider
 * @package App\Modules\Bosslike
 */
class ServiceProvider extends BaseProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InstashopService::class, function () {
            return new InstashopService();
        });

        $this->app->alias(InstashopService::class, 'instashop');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/Views', 'instashop');

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }
}
