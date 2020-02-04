<?php

namespace App\Services\BankAccount;

use \Illuminate\Support\Facades\Facade;

class BankAccountFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\BankAccount\BankAccountService';
    }
}
