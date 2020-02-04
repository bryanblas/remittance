<?php

namespace App\Repositories\Role;

interface RoleInterface
{
    public function getAll($request);

    public function get($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);
}
