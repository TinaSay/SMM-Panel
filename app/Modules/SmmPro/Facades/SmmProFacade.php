<?php

namespace App\Modules\SmmPro\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SmmProFacade
 * @package App\Modules\SmmPro\Facades
 */
class SmmProFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'smmpro';
    }
}
