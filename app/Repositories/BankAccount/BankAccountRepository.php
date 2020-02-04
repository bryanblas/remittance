<?php

namespace App\Repositories\BankAccount;

use App\Models\BankAccount;

class BankAccountRepository implements BankAccountInterface
{
    private $model;

    public function __construct(BankAccount $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $bankAccount = $this->model->find($id);
        if ($bankAccount) {
            $bankAccount->update($request);
            return $bankAccount;
        }
        return false;
    }

    public function delete($id)
    {
        $bankAccount = $this->model->find($id);
        if ($bankAccount) {
            $bankAccount->delete();
            return $bankAccount;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll($filters, $orderBy='created_at', $orderDirection='DESC', $perPage=false, $page=false)
    {
        $model = $this->model;
        foreach ($filters as $key => $value) {
            $model = $model->where($key, $value);
        }
        $model = $model->orderBy($orderBy, $orderDirection);
        if ($perPage !==  false) {
            return $model->paginate($perPage, ['*'], 'page', $page !==  false? $page: 0);
        }
        return $model->get();
    }
}
