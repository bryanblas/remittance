<?php

namespace App\Services\ExchangeRate;

use \Illuminate\Support\Facades\Facade;

class ExchangeRateFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\ExchangeRate\ExchangeRateService';
    }
}
