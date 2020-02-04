<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserInterface
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
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

    public function where($payload, $options = [], $get = 'get')
    {
        $object = $this->model->where($payload);

        if (!empty($options)) {
            foreach ($options as $key => $val) {
                if ($key == 'orderBy' || $key == 'whereIn') {
                    foreach ($val as $k => $v) {
                        $object = $object->$key($k, $v);
                    }
                } else {
                    $object = $object->$key($val);
                }
            }
        }

        return $object->$get();
    }
}
