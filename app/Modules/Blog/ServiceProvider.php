<?php

namespace App\Modules\Blog;

use App\Modules\Blog\Services\BlogManager;
use Illuminate\Support\ServiceProvider as BaseProvider;
use Route;

class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');

        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');

        // Explicit model binding
        Route::bind('blog', function ($id) {
            return \App\Modules\Blog\Models\Blog::findOrFail($id);
        });

        Route::bind('topic', function ($id) {
            return \App\Modules\Blog\Models\Topic::findOrFail($id);
        });

        Route::bind('post', function ($id) {
            return \App\Modules\Blog\Models\Post::findOrFail($id);
        });
    }

    public function register()
    {
        $this->app->singleton(BlogManager::class, function () {
            return new BlogManager;
        });

        $this->app->alias(BlogManager::class, 'blog');
    }
}