<?php

namespace App\Modules\Bosslike;

use App\Modules\Bosslike\Services\BosslikeService;
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
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');

        $this->loadViewsFrom(__DIR__ . '/Views', 'bosslike');

        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(BosslikeService::class, function () {
            return new BosslikeService();
        });

        $this->app->alias(BosslikeService::class, 'bosslike');
    }
}
