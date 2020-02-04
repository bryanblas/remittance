<?php

namespace App\Services\SystemSetting;

use Illuminate\Http\Request;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Services\BaseService;

class SystemSettingService extends BaseService
{
    protected $systemSettingInterface;

    public function __construct(SystemSettingInterface $systemSettingInterface)
    {
        $this->systemSettingInterface = $systemSettingInterface;
    }

    public function getAll()
    {
        return $this->systemSettingInterface->getAll();
    }

    public function getById($id)
    {
        return $this->systemSettingInterface->find($id);
    }

    public function getByProperty($property)
    {
        return $this->systemSettingInterface->getByProperty($property);
    }

    public function create($request)
    {
        if (!isset($request['status'])) {
            $request['status'] = 0;
        }
        return $this->systemSettingInterface->create($request);
    }

    public function update($id, $request)
    {
        if (!isset($request['value'])) {
            $request['value'] = 0;
        }
        return $this->systemSettingInterface->update($id, $request);
    }

    public function delete($id)
    {
        return $this->systemSettingInterface->delete($id);
    }
}
