<?php

namespace App\Repositories\Balance;

use App\Models\Balance;

class BalanceRepository implements BalanceInterface
{
    private $model;

    public function __construct(Balance $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $balance = $this->model->find($id);
        if ($balance) {
            $balance->update($request);
            return $balance;
        }
        return false;
    }

    public function delete($id)
    {
        $balance = $this->model->find($id);
        if ($balance) {
            $balance->delete();
            return $balance;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getByMerchant($merchant_id)
    {
        return $this->model->where('merchant_id', $merchant_id)->get();
    }

    public function findOrCreate($payload)
    {
        return $this->model->firstOrCreate($payload);
    }

    public function getAll($request)
    {
        if (isset($request['per_page'])) {
            return $this->model->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $this->model->get();
    }
}
