<?php

namespace App\Modules\Cart;

use App\Modules\Cart\Services\Cart;
use Illuminate\Support\ServiceProvider as BaseProvider;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        $this->loadViewsFrom(__DIR__.'/Views', 'cart');
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Cart::class, function () {
            return new Cart;
        });

        $this->app->alias(Cart::class, 'cart');
    }
}