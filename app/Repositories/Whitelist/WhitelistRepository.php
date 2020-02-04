<?php

namespace App\Repositories\Whitelist;

use App\Models\Whitelist;

class WhitelistRepository implements WhitelistInterface
{
    private $model;

    public function __construct(Whitelist $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->get();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $payload)
    {
        $user = $this->model->find($id);
        $user->update($payload);
        return $user;
    }

    public function delete($id)
    {
        $user = $this->model->find($id);
        $user->delete();
        return $user;
    }

    public function where($payload)
    {
        return $this->model->where($payload)->get();
    }
}
