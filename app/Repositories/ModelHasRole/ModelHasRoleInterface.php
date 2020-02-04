<?php

namespace App\Repositories\ModelHasRole;

interface ModelHasRoleInterface
{
    public function getAll();

    public function where($payload, $count = 'get');
}
