<?php

namespace App\Services\Bank;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use App\Http\Requests\StoreBankRequest;
use App\Http\Requests\UpdateBankRequest;
use App\Services\BaseService;
use App\Repositories\Bank\BankInterface;

class BankService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING BANK:';
    const LOGS_CREATED  = 'CREATED BANK:';
    const LOGS_UPDATING = 'UPDATING BANK:';
    const LOGS_UPDATED = 'UPDATED BANK:';
    const LOGS_DELETING = 'DELETING BANK:';
    const LOGS_DELETED = 'DELETED BANK:';
    const LOGS_RETRIEVING = 'RETRIEVING BANK:';
    const LOGS_RETRIEVED = 'RETRIEVED BANK:';
    const LOGS_EXPORTING = 'EXPORTING BANKS:';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-BANK-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-BANK-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-BANK-404' , 'message' => 'Bank not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-BANK-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-Banks.xls';
    const EXPORT_HEADERS = ['Bank ID','Code','Name','Active Status','Date Updated','Date Created','Date Deleted'];

    protected $bankInterface;
    protected $excelExport;

    public function __construct(
        BankInterface $bankInterface,
        Excel $excelExport
   ) {
        $this->bankInterface= $bankInterface;
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
            $bank = $this->bankInterface->create($request->all());
            Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($bank));
            return $this->sendResponse($bank->getOriginal(), '');
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
            $bank = $this->bankInterface->update($id, $request->all());
            if ($bank) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($bank));
                return $this->sendResponse($bank->getOriginal(), '');
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
            $bank = $this->bankInterface->delete($id);
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
            $bank = $this->bankInterface->get($id);
            if ($bank) {
                return $this->sendResponse($bank->getOriginal(), '');
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
            $banks = $this->bankInterface->getAll($request);
            return $this->sendResponse($banks->toArray(), '');
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
            $users = $this->bankInterface->getAll($request);
            return $this->excelExport->setData($users->toArray(), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreateRequest($request)
    {
        $storeBankRequest = new StoreBankRequest();
        $rules = $storeBankRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateBankRequest = new UpdateBankRequest();
        $rules = $updateBankRequest->rules();
        return Validator::make($request->all(), $rules);
    }
}
