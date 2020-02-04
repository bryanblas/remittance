<?php

namespace App\Repositories\ExchangeRate;

interface ExchangeRateInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getAll($request);
    public function getSummarized($payload = null);
}
