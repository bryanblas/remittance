<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Bank\BankFacade;

class BankController extends Controller
{
    public function __construct(BankFacade $bank)
    {
        $this->bank = $bank;
    }

    public function create(Request $request)
    {
        return $this->bank::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->bank::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->bank::delete($id, $request);
    }

    public function get($id)
    {
        return $this->bank::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->bank::getAll($request);
    }

    public function export(Request $request)
    {
        return $this->bank::export($request);
    }
}
