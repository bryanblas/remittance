<?php

namespace App\Repositories\PasswordReset;

use App\Models\PasswordReset;

class PasswordResetRepository implements PasswordResetInterface
{
    private $model;

    public function __construct(PasswordReset $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($hash, $payload)
    {
        $passwordReset = $this->get($hash);
        $passwordReset->update($payload);
        return $passwordReset;
    }

    public function getAll($request)
    {
        if (isset($request['per_page'])) {
            return $this->model->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $this->model->get();
    }

    public function get($hash)
    {
        return $this->model->select('*')
            ->where('hash', $hash)
            ->first();
    }
}
