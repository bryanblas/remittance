<?php

namespace App\Repositories\VerifyUser;

use App\Models\VerifyUser;

class VerifyUserRepository implements VerifyUserInterface
{
    private $model;

    public function __construct(VerifyUser $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function verify($hash)
    {
        $verifyUser = $this->getByHash($hash);
        $verifyUser->user->verified = 1;
        $verifyUser->user->save();
        return $verifyUser->user;
    }

    public function getByHash($hash)
    {
        return $this->model->where('token', $hash)->first();
    }
}
