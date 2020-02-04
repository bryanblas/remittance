<?php

namespace App\Services\SystemSetting;

use \Illuminate\Support\Facades\Facade;

class SystemSettingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\SystemSetting\SystemSettingService';
    }
}
