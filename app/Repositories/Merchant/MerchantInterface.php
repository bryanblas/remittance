<?php

namespace App\Repositories\Merchant;

interface MerchantInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getByUserEmail($email);
    public function getAll($request, $filters);
    public function where($payload);
    public function import($request);
}
