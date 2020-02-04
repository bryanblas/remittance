<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ExchangeRate\ExchangeRateFacade;

class ExchangeRateController extends Controller
{
    public function __construct(ExchangeRateFacade $exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    public function create(Request $request)
    {
        return $this->exchangeRate::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->exchangeRate::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->exchangeRate::delete($id, $request);
    }

    public function get($id)
    {
        return $this->exchangeRate::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->exchangeRate::getAll($request);
    }

    public function getSummarized()
    {
        return $this->exchangeRate::getSummarized();
    }

    public function export(Request $request)
    {
        return $this->exchangeRate::export($request);
    }
}
