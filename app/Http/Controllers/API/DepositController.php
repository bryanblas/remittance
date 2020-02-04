<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Deposit\DepositFacade;

class DepositController extends Controller
{
    public function __construct(DepositFacade $deposit)
    {
        $this->deposit = $deposit;
    }

    public function create(Request $request)
    {
        return $this->deposit::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->deposit::update($id, $request);
    }

    public function updateStatus(Request $request, $id)
    {
        return $this->deposit::updateStatus($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->deposit::delete($id, $request);
    }

    public function get($id)
    {
        return $this->deposit::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->deposit::getAll($request);
    }

    public function export(Request $request)
    {
        return $this->deposit::export($request);
    }

    public function importCsv(Request $request)
    {
        return $this->deposit::import($request);
    }
}
