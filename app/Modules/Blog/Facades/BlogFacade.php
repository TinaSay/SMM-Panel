<?php

namespace App\Modules\Blog\Facades;

use Illuminate\Support\Facades\Facade;

class BlogFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'blog';
    }
}