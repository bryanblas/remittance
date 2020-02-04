<?php

namespace App\Repositories\Balance;

interface BalanceInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getByMerchant($merchant_id);
    public function findOrCreate($payload);
    public function getAll($request);
}
