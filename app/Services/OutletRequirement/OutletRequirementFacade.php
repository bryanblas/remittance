<?php

namespace App\Services\OutletRequirement;

use \Illuminate\Support\Facades\Facade;

class OutletRequirementFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\OutletRequirement\OutletRequirementService';
    }
}
