<?php

namespace App\Repositories\ActivityLog;

use App\Models\ActivityLog;

class ActivityLogRepository implements ActivityLogInterface
{
    private $model;

    public function __construct(ActivityLog $model)
    {
        $this->model = $model;
    }

    public function getAll($options = array())
    {
        if (!empty($options)) {
            $object = $this->model;
            foreach ($options as $key => $val) {
                if ($key == 'orderBy') {
                    foreach ($val as $k => $v) {
                        $object = $object->$key($k, $v);
                    }
                } else {
                    $object = $object->$key($val);
                }
            }
            return $object;
        }

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

    public function distinct($field)
    {
        return $this->model->select($field)->distinct($field)->get();
    }

    public function where($payload, $options)
    {
        $object = null;
        if (!empty($options)) {
            $object = $this->model->where($payload);
            foreach ($options as $key => $val) {
                if ($key == 'orderBy') {
                    foreach ($val as $k => $v) {
                        $object = $object->$key($k, $v);
                    }
                } else {
                    $object = $object->$key($val);
                }
            }
            return $object;
        }

        return $this->model->where($payload)->get();
    }
}
