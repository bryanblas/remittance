<?php

namespace App\Repositories\Withdrawal;

use App\Models\Withdrawal;

class WithdrawalRepository implements WithdrawalInterface
{
    private $model;

    public function __construct(Withdrawal $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $withdrawal = $this->model->find($id);
        if ($withdrawal) {
            $withdrawal->update($request);
            return $withdrawal;
        }
        return false;
    }

    public function delete($id)
    {
        $withdrawal = $this->model->find($id);
        if ($withdrawal) {
            $withdrawal->delete();
            return $withdrawal;
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
            $lists = $this->model->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        $lists = $this->model->get();
        foreach ($lists as $index => $value) {
            $value->merchant->email;
            $value->balance->currency;
        }

        return $lists;
    }
}
