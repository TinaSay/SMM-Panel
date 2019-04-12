<?php

namespace App\Modules\Instashop\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class BosslikeFacade
 * @package App\Modules\Bosslike\Facades
 */
class InstashopFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'instashop';
    }
}
