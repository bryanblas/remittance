<?php

namespace App\Services\ExchangeRate;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use App\Http\Requests\StoreExchangeRateRequest;
use App\Http\Requests\UpdateExchangeRateRequest;
use App\Services\BaseService;
use App\Repositories\ExchangeRate\ExchangeRateInterface;

class ExchangeRateService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING EXCHANGE RATE:';
    const LOGS_CREATED  = 'CREATED EXCHANGE RATE:';
    const LOGS_UPDATING = 'UPDATING EXCHANGE RATE:';
    const LOGS_UPDATED = 'UPDATED EXCHANGE RATE:';
    const LOGS_DELETING = 'DELETING EXCHANGE RATE:';
    const LOGS_DELETED = 'DELETED EXCHANGE RATE:';
    const LOGS_RETRIEVING = 'RETRIEVING EXCHANGE RATE:';
    const LOGS_RETRIEVED = 'RETRIEVED EXCHANGE RATE:';
    const LOGS_EXPORTING = 'EXPORTING EXCHANGE RATES:';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-EXCHANGE-RATE-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-EXCHANGE-RATE-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-EXCHANGE-RATE-404' , 'message' => 'Exchange Rate not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-EXCHANGE-RATE-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-Banks.xls';
    const EXPORT_HEADERS = ['Bank ID','Code','Name','Active Status','Date Updated','Date Created','Date Deleted'];

    protected $exchangeRateInterface;
    protected $excelExport;

    public function __construct(
        ExchangeRateInterface $exchangeRateInterface,
        Excel $excelExport
   ) {
        $this->exchangeRateInterface= $exchangeRateInterface;
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
            $exchangeRate = $this->exchangeRateInterface->create($request->all());
            Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($exchangeRate));
            return $this->sendResponse($exchangeRate->getOriginal(), '');
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
            $exchangeRate = $this->exchangeRateInterface->update($id, $request->all());
            if ($exchangeRate) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($exchangeRate));
                return $this->sendResponse($exchangeRate->getOriginal(), '');
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
            $exchangeRate = $this->exchangeRateInterface->delete($id);
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
            $exchangeRate = $this->exchangeRateInterface->get($id);
            if ($exchangeRate) {
                return $this->sendResponse($exchangeRate->getOriginal(), '');
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
            $exchangeRates = $this->exchangeRateInterface->getAll($request);
            return $this->sendResponse($exchangeRates->toArray(), '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function getSummarized()
    {
        try {
            Log::info(self::LOGS_RETRIEVING);
            $exchangeRates = $this->exchangeRateInterface->getSummarized();
            return $this->sendResponse($exchangeRates, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function export($request)
    {
        try {
            LogexchangeRatesinfo(self::LOGS_EXPORTING);
            $users = $this->exchangeRateInterface->getAll($request);
            return $this->excelExport->setData($exchangeRates->toArray(), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreateRequest($request)
    {
        $storeExchangeRateRequest = new StoreExchangeRateRequest();
        $rules = $storeExchangeRateRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateExchangeRateRequest = new UpdateExchangeRateRequest();
        $rules = $updateExchangeRateRequest->rules();
        return Validator::make($request->all(), $rules);
    }
}
