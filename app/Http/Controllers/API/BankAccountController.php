<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\BankAccount\BankAccountFacade;

class BankAccountController extends Controller
{
    public function __construct(BankAccountFacade $bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    public function create(Request $request)
    {
        return $this->bankAccount::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->bankAccount::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->bankAccount::delete($id, $request);
    }

    public function get($id)
    {
        return $this->bankAccount::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->bankAccount::getAll($request);
    }

    public function export(Request $request)
    {
        return $this->bankAccount::export($request);
    }
}
