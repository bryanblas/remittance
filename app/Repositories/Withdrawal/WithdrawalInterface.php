<?php

namespace App\Repositories\Withdrawal;

interface WithdrawalInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getAll($request);
}
