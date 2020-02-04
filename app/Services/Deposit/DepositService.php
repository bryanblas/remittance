<?php

namespace App\Services\Deposit;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreDepositRequest;
use App\Http\Requests\UpdateDepositRequest;
use App\Mail\Deposit\DepositVerifiedMailer;
use App\Services\BaseService;
use App\Repositories\Deposit\DepositInterface;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\Balance\BalanceInterface;
use Illuminate\Support\Facades\DB;

class DepositService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING DEPOSIT:';
    const LOGS_CREATED  = 'CREATED DEPOSIT:';
    const LOGS_UPDATING = 'UPDATING DEPOSIT:';
    const LOGS_UPDATED = 'UPDATED DEPOSIT:';
    const LOGS_DELETING = 'DELETING DEPOSIT:';
    const LOGS_DELETED = 'DELETED DEPOSIT:';
    const LOGS_RETRIEVING = 'RETRIEVING DEPOSIT:';
    const LOGS_RETRIEVED = 'RETRIEVED DEPOSIT:';
    const LOGS_EXPORTING = 'EXPORTING DEPOSITS:';
    const LOGS_IMPORTING = 'IMPORTING DEPOSITS:';

    /*DB FIELDS*/
    const DB_TRANSACTION_NUMBER_FIELD = 'transaction_number';
    const DB_TRANSACTION_DATE_FIELD = 'transaction_date';
    const DB_MERCHANT_ID_FIELD = 'merchant_id';
    const DB_USER_ID_FIELD = 'user_id';
    const DB_BANK_ACCOUNT_ID_FIELD = 'bank_account_id';
    const DB_CURRENCY_FIELD = 'currency';
    const DB_AMOUNT_FIELD = 'amount';
    const DB_FEE_FIELD = 'fee';
    const DB_ROUTE_FIELD = 'route';
    const DB_DEPOSIT_SLIP_FIELD = 'deposit_slip';
    const DB_DEPOSIT_TYPE_FIELD = 'deposit_type';
    const DB_FILENAME_FIELD = 'filename';
    const DB_MESSAGE_FIELD = 'message';
    const DB_STATUS_FIELD = 'status';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-DEPOSIT-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-DEPOSIT-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_MERCHANT = array('code' => 'E-DEPOSIT-003' , 'message' => 'Invalid merchant', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-DEPOSIT-404' , 'message' => 'Deposit not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-DEPOSIT-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'B2B-Deposits.xls';
    const EXPORT_HEADERS = ['Import Date', 'Transaction Number', 'Account Number', 'Currency', 'Amount', 'Fee', 'Deposit Route', 'Process Date', 'Remarks', 'Error', 'Check'];

    const IMPORT_CSV_DELIMETER = ',';
    const DEFAULT_DEPOSIT_STATUS = 'Pending';

    protected $depositInterface;
    protected $merchantInterface;
    protected $balanceInterface;
    protected $excelExport;

    public function __construct(
        DepositInterface $depositInterface,
        MerchantInterface $merchantInterface,
        BalanceInterface $balanceInterface,
        Excel $excelExport
   ) {
        $this->depositInterface = $depositInterface;
        $this->merchantInterface = $merchantInterface;
        $this->balanceInterface = $balanceInterface;
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
            $merchant = $this->merchantInterface->getByUserEmail($request->user()->email);
            if (!empty($merchant)) {
                $payload = $this->getDepositPayload($request);
                $payload[self::DB_MERCHANT_ID_FIELD] = $merchant->id;
                $payload[self::DB_TRANSACTION_NUMBER_FIELD] = $this->createTransactionId($payload[self::DB_CURRENCY_FIELD]);
                $payload[self::DB_STATUS_FIELD] = self::DEFAULT_DEPOSIT_STATUS;
                $deposit = $this->depositInterface->create($payload);
                Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($deposit));
                return $this->sendResponse($deposit->getOriginal(), '');
            }
            Log::info(self::LOGS_CREATING . ' - ' . json_encode($validator->errors()));
            return $this->sendError(self::API_ERROR_INVALID_MERCHANT, $validator->errors());
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
            $payload = $this->getDepositPayload($request);
            $deposit = $this->depositInterface->update($id, $payload);
            if ($deposit) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($deposit));
                return $this->sendResponse($deposit->getOriginal(), '');
            }
            return $this->sendError(self::API_ERROR_NOT_FOUND, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function updateStatus($id, Request $request)
    {
        try {
            Log::info(self::LOGS_UPDATING . $request->user()->email . ' - ' . json_encode($request->all()));
            DB::beginTransaction();
            $deposit = $this->depositInterface->update($id, $request->all());
            if ($deposit) {
                $balances = $this->updateMerchantBalance($deposit);
                Mail::to($deposit->merchant->email)->send(new DepositVerifiedMailer($deposit));
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($deposit));
                DB::commit();
                return $this->sendResponse($deposit->getOriginal(), '');
            }
            return $this->sendError(self::API_ERROR_NOT_FOUND, '');
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function delete($id, $request)
    {
        try {
            Log::info(self::LOGS_DELETING . $request->user()->email . ' - ' . $id);
            $deposit = $this->depositInterface->delete($id);
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
            $deposit = $this->depositInterface->get($id);
            if ($deposit) {
                return $this->sendResponse($deposit->getOriginal(), '');
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
            $deposits = $this->depositInterface->getAll($request);
            foreach ($deposits as $key => $value) {
                $deposits[$key]['bank_account_id'] = $deposits[$key]->BankAccount->number;
                $deposits[$key]['bank_code'] = $deposits[$key]->BankAccount->Bank->code;
                $deposits[$key]['merchant_email'] = $deposits[$key]->Merchant->email;
            }
            return $this->sendResponse($deposits->toArray(), '');
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
            $deposits = $this->depositInterface->getAll($request);
            return $this->excelExport->setData($deposits->toArray(), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function import($request)
    {
        try {
            Log::info(self::LOGS_IMPORTING);
            $data = $this->extractDataCsv($request);
            return $this->sendResponse($data, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_IMPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    private function validateCreateRequest($request)
    {
        $storeDepositRequest = new StoreDepositRequest();
        $rules = $storeDepositRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateDepositRequest = new UpdateDepositRequest();
        $rules = $updateDepositRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function getDepositPayload($request)
    {
        $payload = [
            self::DB_TRANSACTION_DATE_FIELD => $request->transaction_date,
            self::DB_BANK_ACCOUNT_ID_FIELD => $request->bank_account_id,
            self::DB_USER_ID_FIELD => isset($request->user_id) ? $request->user_id : null,
            self::DB_CURRENCY_FIELD => $request->currency,
            self::DB_AMOUNT_FIELD => $request->amount,
            self::DB_FEE_FIELD => $request->fee,
            self::DB_ROUTE_FIELD => $request->route,
            self::DB_DEPOSIT_TYPE_FIELD => $request->deposit_type,
            self::DB_FILENAME_FIELD => $request->filename,
            self::DB_MESSAGE_FIELD => $request->message,
            self::DB_STATUS_FIELD => $request->status
        ];
        $depositSlip = $this->uploadDepositSlip($request);

        if ($depositSlip !== false) {
            $payload[self::DB_DEPOSIT_SLIP_FIELD] = $depositSlip;
        }

        return $payload;
    }

    private function uploadDepositSlip($request)
    {
        if ($request->deposit_slip !== null) {
            $imageName = time().'.'.$request->deposit_slip->getClientOriginalExtension();
            if ($request->deposit_slip->move(public_path(env('DEPOSIT_SLIP_DIR')), $imageName)) {
                return $imageName;
            }
        }
        return false;
    }

    private function extractDataCsv($request)
    {
        $data = [];

        if ($request->csv_file !== null) {
            $filename = $request->csv_file->getPathName();
            $header = null;
            if (($handle = fopen($filename, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, self::IMPORT_CSV_DELIMETER)) !== false) {
                    if (!$header) {
                        $header = $row;
                    } else {
                        $data[] = array_combine($header, $row);
                    }
                }
                fclose($handle);
            }
        }

        return $data;
    }

    private function updateMerchantBalance($response)
    {
        try {
            $data[self::DB_MERCHANT_ID_FIELD] = $response->merchant_id;
            $data[self::DB_CURRENCY_FIELD] = $response->currency;

            $balance = $this->balanceInterface->findOrCreate($data);
            if ($balance) {
                $amount = !is_null($balance->amount) ? $balance->amount : 0;
                $amount = floatval($amount) + floatval($response->amount);
                $updatedBalance = $this->balanceInterface->update($balance->id, [self::DB_AMOUNT_FIELD => $amount]);
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function createTransactionId($currency)
    {
        return sprintf(
            'D%04x%04x-%04x-%04x%04x%04x',
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
