<?php

namespace App\Modules\Support\Facades;

use Illuminate\Support\Facades\Facade;

class SupportFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'help';
    }
}