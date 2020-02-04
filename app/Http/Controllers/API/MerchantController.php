<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Merchant\MerchantFacade;

class MerchantController extends Controller
{
    public function __construct(MerchantFacade $merchant)
    {
        $this->merchant = $merchant;
    }

    public function create(Request $request)
    {
        return $this->merchant::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->merchant::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->merchant::delete($id, $request);
    }

    public function get($id)
    {
        return $this->merchant::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->merchant::getAll($request);
    }

    public function logIn(Request $request)
    {
        return $this->merchant::logIn($request);
    }

    public function updateKycStatus(Request $request, $id)
    {
        return $this->merchant::updateKycStatus($id, $request);
    }

    public function export(Request $request)
    {
        return $this->merchant::export($request);
    }

    public function verifyAccount($hash)
    {
        return $this->merchant::verifyAccount($hash);
    }

    public function importCsv(Request $request)
    {
        return $this->merchant::import($request);
    }
}
