<?php

namespace App\Services\ModelHasRole;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use App\Repositories\ModelHasRole\ModelHasRoleInterface;
use App\Services\BaseService;

class ModelHasRoleService extends BaseService
{
    protected $modelHasRoleInterface;

    public function __construct(ModelHasRoleInterface $modelHasRoleInterface)
    {
        $this->modelHasRoleInterface = $modelHasRoleInterface;
    }

    public function getAll()
    {
        return $this->modelHasRoleInterface->getAll();
    }

    public function getByModelId($id)
    {
        return $this->modelHasRoleInterface->where(['model_id' => $id], 'first');
    }
}
