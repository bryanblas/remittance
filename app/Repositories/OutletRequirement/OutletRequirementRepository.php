<?php

namespace App\Repositories\OutletRequirement;

use App\Models\OutletRequirement;

class OutletRequirementRepository implements OutletRequirementInterface
{
    private $model;

    public function __construct(OutletRequirement $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->get();
    }
}
