<?php

namespace App\Repositories\User;

interface UserInterface
{
    public function getAll($filters, $orderBy=false, $orderDirection=false, $perPage=false, $page=false);

    public function get($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function where($payload, $options = [], $get = 'get');
}
