<?php

namespace App\Repositories\SystemSetting;

interface SystemSettingInterface
{
    public function getAll();

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function where($payload);

    public function getByProperty($property);
}
