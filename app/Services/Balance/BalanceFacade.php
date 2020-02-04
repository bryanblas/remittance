<?php

namespace App\Services\Balance;

use \Illuminate\Support\Facades\Facade;

class BalanceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Balance\BalanceService';
    }
}
