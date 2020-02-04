<?php

namespace App\Services\Withdrawal;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use App\Http\Requests\StoreWithdrawalRequest;
use App\Http\Requests\UpdateWithdrawalRequest;
use App\Services\BaseService;
use App\Repositories\Withdrawal\WithdrawalInterface;
use App\Repositories\ExchangeRate\ExchangeRateInterface;
use App\Repositories\Balance\BalanceInterface;
use App\Repositories\Merchant\MerchantInterface;
use Illuminate\Support\Facades\DB;

class WithdrawalService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING WITHDRAWAL:';
    const LOGS_CREATED  = 'CREATED WITHDRAWAL:';
    const LOGS_UPDATING = 'UPDATING WITHDRAWAL:';
    const LOGS_UPDATED = 'UPDATED WITHDRAWAL:';
    const LOGS_DELETING = 'DELETING WITHDRAWAL:';
    const LOGS_DELETED = 'DELETED WITHDRAWAL:';
    const LOGS_RETRIEVING = 'RETRIEVING WITHDRAWAL:';
    const LOGS_RETRIEVED = 'RETRIEVED WITHDRAWAL:';
    const LOGS_EXPORTING = 'EXPORTING WITHDRAWAL:';

    /*DB FIELDS*/
    const DB_METHOD_FIELD = 'method';
    const DB_TRANSACTION_NUMBER_FIELD = 'transaction_number';
    const DB_TRANSACTION_DATE_FIELD = 'transaction_date';
    const DB_ACCOUNT_ID_FIELD = 'account_id';
    const DB_CURRENCY_FIELD = 'currency';
    const DB_AMOUNT_FIELD = 'amount';
    const DB_FEE_FIELD = 'fee';
    const DB_BANK_ID_FIELD = 'bank_id';
    const DB_SEND_BACK_FIELD = 'send_back';
    const DB_STATUS_FIELD = 'status';
    const DB_RATE_FIELD = 'rate';
    const DB_DEPOSIT_SLIP_FIELD = 'deposit_slip';
    const DB_MERCHANT_BALANCE_ID = 'merchant_balance_id';

    const DB_MERCHANT_ID_FIELD = 'merchant_id';
    const DB_CURRENCY_FROM = 'currency_from';
    const DB_CURRENCY_TO = 'currency_to';
    const DB_MERCHANT_CURRENCY_FROM = 'merchant_currency_from';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-WITHDRAWAL-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-WITHDRAWAL-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_NO_BALANCE_FOUND = array('code' => 'E-WITHDRAWAL-003' , 'message' => 'No Balance found', 'http_code' => 400);
    const API_ERROR_NO_RATE_FOUND = array('code' => 'E-WITHDRAWAL-004' , 'message' => 'No Exchange Rate found on specified currency', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-WITHDRAWAL-404' , 'message' => 'Withdrawal not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-WITHDRAWAL-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    const DEFAULT_DEPOSIT_STATUS = 'Pending';

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-Withdrawals.xls';
    const EXPORT_HEADERS = ['Bank ID','Code','Name','Active Status','Date Updated','Date Created','Date Deleted'];

    protected $withdrawalInterface;
    protected $exchangeRateInterface;
    protected $balanceInterface;
    protected $merchantInterface;
    protected $excelExport;

    public function __construct(
        WithdrawalInterface $withdrawalInterface,
        ExchangeRateInterface $exchangeRateInterface,
        BalanceInterface $balanceInterface,
        MerchantInterface $merchantInterface,
        Excel $excelExport
   ) {
        $this->withdrawalInterface= $withdrawalInterface;
        $this->balanceInterface = $balanceInterface;
        $this->exchangeRateInterface = $exchangeRateInterface;
        $this->merchantInterface = $merchantInterface;
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
            $payload = $request->all();
            $merchant = $this->merchantInterface->getByUserEmail($request->user()->email);

            $data[self::DB_MERCHANT_ID_FIELD] = $payload[self::DB_MERCHANT_ID_FIELD] = $merchant->id;
            $data[self::DB_CURRENCY_FIELD] = $payload[self::DB_MERCHANT_CURRENCY_FROM];
            $balance = $this->balanceInterface->findOrCreate($data);

            if ($balance) {
                if ($balance->currency == $payload[self::DB_CURRENCY_FIELD]) {
                    $rate = 1;
                } else {
                    $ratePayload = [
                        self::DB_CURRENCY_FROM => $balance->currency,
                        self::DB_CURRENCY_TO => $payload[self::DB_CURRENCY_FIELD]
                    ];

                    $rate = $this->exchangeRateInterface->getSummarized($ratePayload);
                }
                if ($rate) {
                    DB::beginTransaction();
                    $payload[self::DB_MERCHANT_BALANCE_ID] = $balance->id;
                    $payload[self::DB_TRANSACTION_NUMBER_FIELD] = $this->createTransactionId($payload[self::DB_CURRENCY_FIELD]);
                    $payload[self::DB_STATUS_FIELD] = self::DEFAULT_DEPOSIT_STATUS;
                    $payload[self::DB_RATE_FIELD] = isset($rate->rate) ? $rate->rate : $rate;
                    $withdrawal = $this->withdrawalInterface->create($payload);
                    $balances = $this->updateMerchantBalance($balance, $payload[self::DB_AMOUNT_FIELD]);

                    DB::commit();
                    Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($withdrawal));
                    return $this->sendResponse($withdrawal->getOriginal(), '');
                }
                Log::info(self::LOGS_CREATING . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_NO_RATE_FOUND, $validator->errors());
            }
            Log::info(self::LOGS_CREATING . ' - ' . json_encode($validator->errors()));
            return $this->sendError(self::API_ERROR_NO_BALANCE_FOUND, $validator->errors());
        } catch (Exception $e) {
            DB::rollBack();
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
            //$payload = $this->getWithdrawalPayload($request);
            $withdrawal = $this->withdrawalInterface->update($id, $request->all());
            if ($withdrawal) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($withdrawal));
                return $this->sendResponse($withdrawal->getOriginal(), '');
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
            $withdrawal = $this->withdrawalInterface->delete($id);
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
            $withdrawal = $this->withdrawalInterface->get($id);
            if ($withdrawal) {
                return $this->sendResponse($withdrawal->getOriginal(), '');
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
            $withdrawals = $this->withdrawalInterface->getAll($request);
            return $this->sendResponse($withdrawals->toArray(), '');
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
            $withdrawals = $this->withdrawalInterface->getAll($request);
            return $this->excelExport->setData($withdrawals->toArray(), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreateRequest($request)
    {
        $storeWithdrawalRequest = new StoreWithdrawalRequest();
        $rules = $storeWithdrawalRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateWithdrawalRequest = new UpdateWithdrawalRequest();
        $rules = $updateWithdrawalRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function getWithdrawalPayload($request)
    {
        $payload = [
            self::DB_METHOD_FIELD => $request->method,
            self::DB_TRANSACTION_NUMBER_FIELD => $request->transaction_number,
            self::DB_TRANSACTION_DATE_FIELD => $request->transaction_date,
            self::DB_ACCOUNT_ID_FIELD => $request->account_id,
            self::DB_CURRENCY_FIELD => $request->currency,
            self::DB_AMOUNT_FIELD => $request->amount,
            self::DB_FEE_FIELD => $request->fee,
            self::DB_BANK_ID_FIELD => $request->bank_id,
            self::DB_SEND_BACK_FIELD => $request->send_back,
            self::DB_STATUS_FIELD => $request->status,
            self::DB_RATE_FIELD => $request->rate
        ];
        return $payload;
    }

    private function updateMerchantBalance($balance, $debit)
    {
        try {
            $amount = floatval($balance->amount) - floatval($debit);
            $updatedBalance = $this->balanceInterface->update($balance->id, [self::DB_AMOUNT_FIELD => $amount]);
            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function createTransactionId($currency)
    {
        return sprintf(
            'W%04x%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
