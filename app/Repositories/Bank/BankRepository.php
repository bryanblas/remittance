<?php

namespace App\Repositories\Bank;

use App\Models\Bank;

class BankRepository implements BankInterface
{
    private $model;

    public function __construct(Bank $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $bank = $this->model->find($id);
        if ($bank) {
            $bank->update($request);
            return $bank;
        }
        return false;
    }

    public function delete($id)
    {
        $bank = $this->model->find($id);
        if ($bank) {
            $bank->delete();
            return $bank;
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
}
