<?php

namespace App\Services\BankAccount;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Services\BaseService;
use App\Repositories\BankAccount\BankAccountInterface;

class BankAccountService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING BANKACCOUNT:';
    const LOGS_CREATED  = 'CREATED BANKACCOUNT:';
    const LOGS_UPDATING = 'UPDATING BANKACCOUNT:';
    const LOGS_UPDATED = 'UPDATED BANKACCOUNT:';
    const LOGS_DELETING = 'DELETING BANKACCOUNT:';
    const LOGS_DELETED = 'DELETED BANKACCOUNT:';
    const LOGS_RETRIEVING = 'RETRIEVING BANKACCOUNT:';
    const LOGS_RETRIEVED = 'RETRIEVED BANKACCOUNT:';
    const LOGS_EXPORTING = 'EXPORTING BANKACCOUNTS:';

    /*DB FILTER FIELDS*/
    const DB_FILTER_FIELDS = ['type',  'bank_branch', 'name', 'number'];
    const DB_DEFAULT_ORDERING = 'created_at';
    const DB_DEFAULT_ORDER_DIRECTION = 'DESC';

    /*RESPONSE FIELD*/
    const API_BANK_CODE_FIELD = 'bank_code';
    const API_BANK_NAME_FIELD = 'bank_name';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-BANKACCOUNT-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-BANKACCOUNT-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-BANKACCOUNT-404' , 'message' => 'BankAccount not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-BANKACCOUNT-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-BankAccounts.xls';
    const EXPORT_HEADERS = ['ID','Account Number','Account Name','Bank Code','Bank Name','Bank Branch','Active Status'];

    protected $bankAccountInterface;
    protected $excelExport;
    protected $storeBankAccountRequest;

    public function __construct(
        BankAccountInterface $bankAccountInterface,
        Excel $excelExport
   ) {
        $this->bankAccountInterface= $bankAccountInterface;
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
            $bankAccount = $this->bankAccountInterface->create($request->all());
            Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($bankAccount));
            return $this->sendResponse($bankAccount->getOriginal(), '');
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
            $bankAccount = $this->bankAccountInterface->update($id, $request->all());
            if ($bankAccount) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($bankAccount));
                return $this->sendResponse($bankAccount->getOriginal(), '');
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
            $bankAccount = $this->bankAccountInterface->delete($id);
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
            $bankAccount = $this->bankAccountInterface->get($id);
            if ($bankAccount) {
                $data = $this->formatBankAccountData($bankAccount);
                return $this->sendResponse($data, '');
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
            $perPage = isset($request['per_page']) ? $request['per_page']: false;
            $page = isset($request['page'])? $request['page']: false;
            $orderBy = isset($request['order_by']) ? $request['order_by']: self::DB_DEFAULT_ORDERING;
            $orderDirection = isset($request['order_direction']) ? $request['order_direction']: self::DB_DEFAULT_ORDER_DIRECTION;
            $filters = $this->getFilterParamters($request, self::DB_FILTER_FIELDS);
            $bankAccounts = $this->bankAccountInterface->getAll($filters, $orderBy, $orderDirection, $perPage, $page);
            $data = $this->formatBankAccountData($bankAccounts);
            return $this->sendResponse($data, '');
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
            $bankAccounts = $this->bankAccountInterface->getAll($request);
            return $this->excelExport->setData($this->formatExportData($bankAccounts), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    protected function addExtraData($bankAccounts)
    {
        $bankAccountList = array();
        foreach ($bankAccounts as $bankAccount) {
            $bankAccountData = $bankAccount->getOriginal();
            $bankAccountData[self::API_BANK_CODE_FIELD] = isset($bankAccount->bank->code)? $bankAccount->bank->code : '';
            $bankAccountData[self::API_BANK_NAME_FIELD] = isset($bankAccount->bank->name)? $bankAccount->bank->name : '';
            $bankAccountList[] = $bankAccountData;
        }
        return $bankAccountList;
    }

    private function formatExportData($bankAccounts)
    {
        $exportList = array();
        foreach ($bankAccounts as $bankAccount) {
            $bankCode = isset($bankAccount->bank->code) ? $bankAccount->bank->code: '';
            $bankName = isset($bankAccount->bank->name) ? $bankAccount->bank->name: '';
            $exportList[] = [
                $bankAccount->id,
                $bankAccount->number,
                $bankAccount->name,
                $bankCode,
                $bankName,
                $bankAccount->bank_branch,
                $bankAccount->status
            ];
        }
        return $exportList;
    }

    private function validateCreateRequest($request)
    {
        $storeBankAccountRequest = new StoreBankAccountRequest();
        $rules = $storeBankAccountRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateBankAccountRequest = new UpdateBankAccountRequest();
        $rules = $updateBankAccountRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function formatBankAccountData($bankAccounts)
    {
        if (isset($bankAccounts->id)) {
            $bankAccounts = $this->addExtraData([$bankAccounts]);
            return array_pop($bankAccounts);
        }

        $bankAccountList = $this->addExtraData($bankAccounts);
        if (!is_array($bankAccounts) && property_exists($bankAccounts, 'total')) {
            $result = $bankAccounts->toArray();
            $result['data'] = $bankAccountList;
            return $result;
        }
        return $bankAccountList;
    }
}
