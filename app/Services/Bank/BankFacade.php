<?php

namespace App\Services\Bank;

use \Illuminate\Support\Facades\Facade;

class BankFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Bank\BankService';
    }
}
