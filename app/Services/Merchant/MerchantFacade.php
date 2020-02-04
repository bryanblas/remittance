<?php

namespace App\Services\Merchant;

use \Illuminate\Support\Facades\Facade;

class MerchantFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Merchant\MerchantService';
    }
}
