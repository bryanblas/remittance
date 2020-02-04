<?php

namespace App\Repositories\Merchant;

use App\Models\Merchant;

class MerchantRepository implements MerchantInterface
{
    private $model;

    public function __construct(Merchant $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $merchant = $this->model->find($id);
        if ($merchant) {
            $merchant->update($request);
            return $merchant;
        }
        return false;
    }

    public function delete($id)
    {
        $merchant = $this->model->find($id);
        if ($merchant) {
            $merchant->delete();
            return $merchant;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getByUserEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getAll($request, $filters)
    {
        $model = $this->model;

        foreach ($filters as $key => $value) {
            $model = $model->where($key, 'LIKE', '%' . $value . '%');
        }

        if (isset($request['per_page'])) {
            return $model->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $model->get();
    }

    public function where($payload)
    {
        return $this->model->where($payload)->get();
    }

    public function import($request)
    {
        return $this->model->import($request);
    }
}
