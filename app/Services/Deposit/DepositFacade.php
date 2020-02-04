<?php

namespace App\Services\Deposit;

use \Illuminate\Support\Facades\Facade;

class DepositFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Deposit\DepositService';
    }
}
