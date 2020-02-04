<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Balance\BalanceFacade;

class BalanceController extends Controller
{
    public function __construct(BalanceFacade $balance)
    {
        $this->balance = $balance;
    }

    public function create(Request $request)
    {
        return $this->balance::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->balance::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->balance::delete($id, $request);
    }

    public function get($id)
    {
        return $this->balance::get($id);
    }

    public function getByMerchant(Request $request, $merchant_id)
    {
        return $this->balance::getByMerchant($request, $merchant_id);
    }

    public function getAll(Request $request)
    {
        return $this->balance::getAll($request);
    }

    public function export(Request $request)
    {
        return $this->balance::export($request);
    }
}
