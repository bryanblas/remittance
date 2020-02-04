<?php

namespace App\Repositories\ModelHasRole;

use App\Models\ModelHasRole;

class ModelHasRoleRepository implements ModelHasRoleInterface
{
    private $model;

    public function __construct(ModelHasRole $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function where($payload, $count = 'get')
    {
        return $this->model->where($payload)->$count();
    }
}
