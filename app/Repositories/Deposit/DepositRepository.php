<?php

namespace App\Repositories\Deposit;

use App\Models\Deposit;
use DB;

class DepositRepository implements DepositInterface
{
    private $model;

    const PAYMENT_COMPLETED = 'Completed';

    public function __construct(Deposit $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $deposit = $this->model->find($id);
        if ($deposit) {
            $deposit->update($request);
            return $deposit;
        }
        return false;
    }

    public function delete($id)
    {
        $deposit = $this->model->find($id);
        if ($deposit) {
            $deposit->delete();
            return $deposit;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getAll($request)
    {
        if (isset($request['per_page'])) {
            return $this->model->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $this->model->get();
    }

    public function import($request)
    {
        return $this->model->import($request);
    }
}
