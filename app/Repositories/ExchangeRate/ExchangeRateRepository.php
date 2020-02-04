<?php

namespace App\Repositories\ExchangeRate;

use App\Models\ExchangeRate;

class ExchangeRateRepository implements ExchangeRateInterface
{
    private $model;

    public function __construct(ExchangeRate $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        return $this->model->create($request);
    }

    public function update($id, $request)
    {
        $exchange_rate = $this->model->find($id);
        if ($exchange_rate) {
            $exchange_rate->update($request);
            return $exchange_rate;
        }
        return false;
    }

    public function delete($id)
    {
        $exchange_rate = $this->model->find($id);
        if ($exchange_rate) {
            $exchange_rate->delete();
            return $exchange_rate;
        }
        return false;
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function getSummarized($payload = null)
    {
        $summarized = [];
        if (!is_null($payload)) {
            $summarized = $this->model->where($payload)->orderBy('created_at', 'DESC')->first();
        } else {
            $columns = $this->model->select('currency_from', 'currency_to')->distinct()->get();
            foreach ($columns as $col) {
                $result = $this->model->where([
                    'currency_from' => $col->currency_from,
                    'currency_to' => $col->currency_to,
                ])->orderBy('created_at', 'DESC')->first();
                array_push($summarized, $result->toArray());
            }
        }


        return $summarized;
    }

    public function getAll($request)
    {
        if (isset($request['per_page'])) {
            return $this->model->orderBy('created_at', 'DESC')->paginate($request['per_page'], ['*'], 'page', $request['page']);
        }
        return $this->model->orderBy('created_at', 'DESC')->get();
    }
}
