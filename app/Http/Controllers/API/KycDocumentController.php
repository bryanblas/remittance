<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\KycDocument\KycDocumentFacade;

class KycDocumentController extends Controller
{
    public function __construct(KycDocumentFacade $document)
    {
        $this->document = $document;
    }

    public function create(Request $request)
    {
        return $this->document::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->document::update($id, $request);
    }

    public function updateStatus(Request $request, $id)
    {
        return $this->document::updateStatus($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->document::delete($id, $request);
    }

    public function getByMerchantId($merchant_id)
    {
        return $this->document::getByMerchant($merchant_id);
    }
}
