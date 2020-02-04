<?php

namespace App\Repositories\PasswordReset;

interface PasswordResetInterface
{
    public function getAll($request);

    public function get($hash);

    public function create($request);

    public function update($hash, $request);
}
