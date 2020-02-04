<?php

namespace App\Services\Permission;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use App\Repositories\Permission\PermissionInterface;
use App\Services\BaseService;

class PermissionService extends BaseService
{
    protected $permissionInterface;

    public function __construct(PermissionInterface $permissionInterface)
    {
        $this->permissionInterface = $permissionInterface;
    }

    public function get()
    {
        return $this->permissionInterface->getAll();
    }

    public function getById($id)
    {
        return $this->permissionInterface->find($id);
    }

    public function create($request)
    {
        if (!isset($request['guard_name'])) {
            $request['guard_name'] = 'web';
        }
        return $this->permissionInterface->create($request);
    }

    public function update($id, $request)
    {
        return $this->permissionInterface->update($id, $request);
    }

    public function delete($id)
    {
        return $this->permissionInterface->delete($id);
    }
}
