<?php

namespace App\Services\Whitelist;

use \Illuminate\Support\Facades\Facade;

class WhitelistFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\Whitelist\WhitelistService';
    }
}
