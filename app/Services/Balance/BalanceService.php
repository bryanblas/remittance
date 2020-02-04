<?php

namespace App\Services\Balance;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use App\Http\Requests\StoreBalanceRequest;
use App\Http\Requests\UpdateBalanceRequest;
use App\Services\BaseService;
use App\Repositories\Balance\BalanceInterface;

class BalanceService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING BALANCE:';
    const LOGS_CREATED  = 'CREATED BALANCE:';
    const LOGS_UPDATING = 'UPDATING BALANCE:';
    const LOGS_UPDATED = 'UPDATED BALANCE:';
    const LOGS_DELETING = 'DELETING BALANCE:';
    const LOGS_DELETED = 'DELETED BALANCE:';
    const LOGS_RETRIEVING = 'RETRIEVING BALANCE:';
    const LOGS_RETRIEVED = 'RETRIEVED BALANCE:';
    const LOGS_EXPORTING = 'EXPORTING BALANCES:';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-BALANCE-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-BALANCE-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-BALANCE-404' , 'message' => 'Balance not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-BALANCE-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-Banks.xls';
    const EXPORT_HEADERS = ['Bank ID','Code','Name','Active Status','Date Updated','Date Created','Date Deleted'];

    protected $balanceInterface;
    protected $excelExport;

    public function __construct(
        BalanceInterface $balanceInterface,
        Excel $excelExport
   ) {
        $this->balanceInterface= $balanceInterface;
        $this->excelExport = $excelExport;
    }

    public function create(Request $request)
    {
        try {
            Log::info(self::LOGS_CREATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $validator = $this->validateCreateRequest($request);
            if ($validator->fails()) {
                Log::info(self::LOGS_CREATING . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_CREATE_REQUEST, $validator->errors());
            }
            $balance = $this->balanceInterface->create($request->all());
            Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($balance));
            return $this->sendResponse($balance->getOriginal(), '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function update($id, Request $request)
    {
        try {
            Log::info(self::LOGS_UPDATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $validator = $this->validateUpdateRequest($request);
            if ($validator->fails()) {
                Log::info(self::LOGS_UPDATING . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_UPDATE_REQUEST, $validator->errors());
            }
            $balance = $this->balanceInterface->update($id, $request->all());
            if ($balance) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($balance));
                return $this->sendResponse($balance->getOriginal(), '');
            }
            return $this->sendError(self::API_ERROR_NOT_FOUND, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function delete($id, $request)
    {
        try {
            Log::info(self::LOGS_DELETING . $request->user()->email . ' - ' . $id);
            $balance = $this->balanceInterface->delete($id);
            Log::info(self::LOGS_DELETED . $request->user() . ' - ' . $id);
            return $this->sendResponse([], '');
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
            $balance = $this->balanceInterface->get($id);
            if ($balance) {
                return $this->sendResponse($balance->getOriginal(), '');
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
            $balances = $this->balanceInterface->getAll($request);
            return $this->sendResponse($balances->toArray(), '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function getByMerchant($request, $merchant_id)
    {
        try {
            Log::info(self::LOGS_RETRIEVING);
            $balances = $this->balanceInterface->getByMerchant($merchant_id);
            return $this->sendResponse($balances->toArray(), '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function export($request)
    {
        try {
            Log::info(self::LOGS_EXPORTING);
            $users = $this->balanceInterface->getAll($request);
            return $this->excelExport->setData($users->toArray(), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreateRequest($request)
    {
        $storeBalanceRequest = new StoreBalanceRequest();
        $rules = $storeBalanceRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateBalanceRequest = new UpdateBalanceRequest();
        $rules = $updateBalanceRequest->rules();
        return Validator::make($request->all(), $rules);
    }
}
