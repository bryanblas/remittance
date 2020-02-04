<?php

namespace App\Repositories\ActivityLog;

interface ActivityLogInterface
{
    public function getAll($options = array());

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function distinct($field);

    public function where($payload, $options);
}
