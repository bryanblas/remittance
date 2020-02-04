<?php

namespace App\Services\ActivityLog;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use App\Repositories\ActivityLog\ActivityLogInterface;
use App\Repositories\SystemSetting\SystemSettingInterface;
use App\Services\BaseService;

class ActivityLogService extends BaseService
{
    protected $activityLogInterface;
    protected $systemSettingInterface;
    protected $pagination;

    public function __construct(
        ActivityLogInterface $activityLogInterface,
        SystemSettingInterface $systemSettingInterface
    ) {
        $this->activityLogInterface = $activityLogInterface;
        $this->systemSettingInterface = $systemSettingInterface;

        $setting = $this->systemSettingInterface->getByProperty('PAGINATION_VALUE');

        $this->pagination = (!empty($setting) && isset($setting->value)) ? $setting->value : 10;
    }

    public function getAll()
    {
        $options = [
            'orderBy' => ['created_at' => 'DESC'],
            'paginate' => $this->pagination,
            'onEachSide' => 2,
        ];
        return $this->activityLogInterface->getAll($options);
    }

    public function getById($id)
    {
        return $this->activityLogInterface->find($id);
    }

    public function getModules()
    {
        return $this->activityLogInterface->distinct('log_name');
    }

    public function filter($payload)
    {
        $filter = array();
        if (isset($payload['start_date'])) {
            $filter[] = ['created_at' , '>=', date('Y-m-d', strtotime($payload['start_date'])) . ' 00:00:00'];
        }
        if (isset($payload['end_date'])) {
            $filter[] = ['created_at' , '<=', date('Y-m-d', strtotime($payload['end_date'])) . ' 23:59:59'];
        }
        if (isset($payload['log_name'])) {
            $filter['log_name'] = $payload['log_name'];
        }
        if (isset($payload['causer_id'])) {
            $filter['causer_id'] = $payload['causer_id'];
        }

        $options = [
            'orderBy' => ['created_at' => 'DESC'],
            'paginate' => $this->pagination,
            'onEachSide' => 2
        ];

        $return = $this->activityLogInterface->where($filter, $options);

        foreach ($payload as $key => $val) {
            $return = $return->appends($key, $val);
        }

        return $return;
    }
}
