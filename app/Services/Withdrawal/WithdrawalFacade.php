<?php

namespace App\Services\Withdrawal;

use \Illuminate\Support\Facades\Facade;

class WithdrawalFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Withdrawal\WithdrawalService';
    }
}
