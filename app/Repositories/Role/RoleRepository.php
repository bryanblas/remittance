<?php

namespace App\Repositories\Role;

use Spatie\Permission\Models\Role;

class RoleRepository implements RoleInterface
{
    private $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $payload)
    {
        $role = $this->model->find($id);
        $role->update($payload);
        return $role;
    }

    public function delete($id)
    {
        $role = $this->model->find($id);
        $role->delete();
        return $role;
    }

    public function getAll($request)
    {
        if (isset($request['per_page'])) {
            return $this->model->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $this->model->get();
    }

    public function get($id)
    {
        return $this->model->find($id);
    }
}
