<?php

namespace App\Repositories\Whitelist;

interface WhitelistInterface
{
    public function getAll();

    public function find($id);

    public function create($request);

    public function update($id, $request);

    public function delete($id);

    public function where($payload);
}
