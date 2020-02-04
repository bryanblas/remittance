<?php

namespace App\Repositories\Deposit;

interface DepositInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getAll($request);
    public function import($request);
}
