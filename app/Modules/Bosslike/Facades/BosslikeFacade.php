<?php

namespace App\Modules\Bosslike\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class BosslikeFacade
 * @package App\Modules\Bosslike\Facades
 */
class BosslikeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bosslike';
    }
}
