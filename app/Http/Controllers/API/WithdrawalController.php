<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Withdrawal\WithdrawalFacade;

class WithdrawalController extends Controller
{
    public function __construct(WithdrawalFacade $withdrawal)
    {
        $this->withdrawal = $withdrawal;
    }

    public function create(Request $request)
    {
        return $this->withdrawal::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->withdrawal::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->withdrawal::delete($id, $request);
    }

    public function get($id)
    {
        return $this->withdrawal::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->withdrawal::getAll($request);
    }

    public function export(Request $request)
    {
        return $this->withdrawal::export($request);
    }
}
