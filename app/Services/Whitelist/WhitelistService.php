<?php

namespace App\Services\Whitelist;

use Illuminate\Http\Request;
use App\Repositories\Whitelist\WhitelistInterface;
use App\Services\BaseService;

class WhitelistService extends BaseService
{
    protected $whitelistInterface;

    public function __construct(WhitelistInterface $whitelistInterface)
    {
        $this->whitelistInterface = $whitelistInterface;
    }

    public function getAll()
    {
        return $this->whitelistInterface->getAll();
    }

    public function getById($id)
    {
        return $this->whitelistInterface->find($id);
    }

    public function getByActiveIP($ip_address)
    {
        return $this->whitelistInterface->where([
            'ip_address' => $ip_address,
            'status' => 1
        ]);
    }



    public function create($request)
    {
        if (!isset($request['status'])) {
            $request['status'] = 0;
        }
        return $this->whitelistInterface->create($request);
    }

    public function update($id, $request)
    {
        if (!isset($request['status'])) {
            $request['status'] = 0;
        }
        return $this->whitelistInterface->update($id, $request);
    }

    public function delete($id)
    {
        return $this->whitelistInterface->delete($id);
    }
}
