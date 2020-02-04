<?php

namespace App\Services\OutletRequirement;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Repositories\OutletRequirement\OutletRequirementInterface;

class OutletRequirementService extends BaseService
{
    /*LOGS*/
    const LOGS_RETRIEVING = 'RETRIEVING Outlet Requirement:';
    const LOGS_RETRIEVED = 'RETRIEVED Outlet Requirement:';

    /*DB FILTER FIELDS*/
    const DB_FILTER_FIELDS = ['outlet_type',  'status'];
    const DB_DEFAULT_ORDERING = 'created_at';
    const DB_DEFAULT_ORDER_DIRECTION = 'DESC';

    /*API ERROR RESPONSE*/
    const API_ERROR_NOT_FOUND = array('code' => 'E-OUTLET-REQUIREMENT-404' , 'message' => 'Bank not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-OUTLET-REQUIREMENT-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    protected $uutletRequirementInterface;

    public function __construct(
        OutletRequirementInterface $outletRequirementInterface
   ) {
        $this->outletRequirementInterface= $outletRequirementInterface;
    }


    public function getAll($request)
    {
        try {
            Log::info(self::LOGS_RETRIEVING);

            $perPage = isset($request['per_page']) ? $request['per_page']: false;
            $page = isset($request['page'])? $request['page']: false;
            $orderBy = isset($request['order_by']) ? $request['order_by']: self::DB_DEFAULT_ORDERING;
            $orderDirection = isset($request['order_direction']) ? $request['order_direction']: self::DB_DEFAULT_ORDER_DIRECTION;
            $filters = $this->getFilterParamters($request, self::DB_FILTER_FIELDS);
            $outletRequirement = $this->outletRequirementInterface->getAll($filters, $orderBy, $orderDirection, $perPage, $page);
            return $this->sendResponse($outletRequirement->toArray(), '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }
}
