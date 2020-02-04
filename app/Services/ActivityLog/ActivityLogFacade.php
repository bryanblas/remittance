<?php

namespace App\Services\ActivityLog;

use \Illuminate\Support\Facades\Facade;

class ActivityLogFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\ActivityLog\ActivityLogService';
    }
}
