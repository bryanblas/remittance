<?php

namespace App\Repositories\BankAccount;

interface BankAccountInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getAll($filters, $orderBy=false, $orderDirection=false, $perPage=false, $page=false);
}
