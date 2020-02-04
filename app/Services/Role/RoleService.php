<?php

namespace App\Services\Role;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use App\Repositories\Role\RoleInterface;
use App\Repositories\ModelHasRole\ModelHasRoleInterface;
use App\Services\BaseService;

class RoleService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING ROLE:';
    const LOGS_CREATED  = 'CREATED ROLE:';
    const LOGS_UPDATING = 'UPDATING ROLE:';
    const LOGS_UPDATED = 'UPDATED ROLE:';
    const LOGS_DELETING = 'DELETING ROLE:';
    const LOGS_DELETED = 'DELETED ROLE:';
    const LOGS_RETRIEVING = 'RETRIEVING ROLE:';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-ROLE-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-ROLE-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-ROLE-404' , 'message' => 'Role not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-ROLE-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    protected $roleInterface;
    protected $modelHasRoleInterface;

    public function __construct(RoleInterface $roleInterface, ModelHasRoleInterface $modelHasRoleInterface)
    {
        $this->roleInterface = $roleInterface;
        $this->modelHasRoleInterface = $modelHasRoleInterface;
    }

    public function create($request)
    {
        try {
            Log::info(self::LOGS_CREATING . $request->user()->email . ' - ' . json_encode($request->all()));
            /**
            @TODO code
            */
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function update($id, $request)
    {
        try {
            Log::info(self::LOGS_UPDATING . $request->user()->email . ' - ' . json_encode($request->all()));
            /**
            @TODO code
            */
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function delete($id)
    {
        try {
            Log::info(self::LOGS_DELETING . $request->user()->email . ' - ' . json_encode($id));
            /**
            @TODO code
            */
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_DELETING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function get($id)
    {
        try {
            Log::info(self::LOGS_RETRIEVING . ' - ' . $id);
            $role = $this->roleInterface->get($id);
            if ($role) {
                return $this->sendResponse($role->getOriginal(), '');
            } else {
                return $this->sendError(self::API_ERROR_NOT_FOUND, '');
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function getAll($request)
    {
        try {
            Log::info(self::LOGS_RETRIEVING);
            $outlets = $this->roleInterface->getAll($request);
            return $this->sendResponse($outlets->toArray(), '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }
}
