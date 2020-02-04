<?php

namespace App\Repositories\SubmittedOutletRequirement;

use App\Models\SubmittedOutletRequirement;

class SubmittedOutletRequirementRepository implements SubmittedOutletRequirementInterface
{
    private $model;

    public function __construct(SubmittedOutletRequirement $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->updateOrCreate(
            ['outlet_requirements_id' => $request['outlet_requirements_id'], 'outlet_id' => $request['outlet_id']],
            $request
        );
    }

    public function update($id, $request)
    {
        $submittedOutletRequirement = $this->model->find($id);
        if ($submittedOutletRequirement) {
            $submittedOutletRequirement->update($request);
            return $submittedOutletRequirement;
        }
        return false;
    }

    public function delete($id)
    {
        $submittedOutletRequirement = $this->model->find($id);
        if ($submittedOutletRequirement) {
            $submittedOutletRequirement->delete();
            return $submittedOutletRequirement;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll($filters, $orderBy='created_at', $orderDirection='DESC', $perPage=false, $page=false)
    {
        $model = $this->model;
        foreach ($filters as $key => $value) {
            $model = $model->where($key, $value);
        }
        $model = $model->orderBy($orderBy, $orderDirection);
        if ($perPage !==  false) {
            return $model->paginate($perPage, ['*'], 'page', $page !==  false? $page: 0);
        }
        return $model->get();
    }
}
