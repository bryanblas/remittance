<?php

namespace App\Repositories\KycDocument;

interface KycDocumentInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function getByMerchant($merchant_id);
    public function getAll($request);
}
