<?php

namespace App\Repositories\SubmittedOutletRequirement;

interface SubmittedOutletRequirementInterface
{
    public function create($request);
    public function update($id, $request);
    public function delete($id);
    public function get($id);
    public function getAll($filters, $orderBy='created_at', $orderDirection='DESC', $perPage=false, $page=false);
}
