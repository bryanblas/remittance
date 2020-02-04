<?php

namespace App\Services\ModelHasRole;

use \Illuminate\Support\Facades\ModelHasRole;

class ModelHasRoleFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Services\ModelHasRole\ModelHasRoleService';
    }
}
