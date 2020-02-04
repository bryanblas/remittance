<?php

namespace App\Services\Merchant;

use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Exports\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\NewRegisteredUserMailer;
use App\Mail\User\VerificationUserMailer;
use App\Http\Requests\StoreMerchantRequest;
use App\Http\Requests\UpdateMerchantRequest;
use App\Mail\KycDocument\KycUpdateMailer;
use App\Services\BaseService;
use App\Repositories\User\UserInterface;
use App\Repositories\Merchant\MerchantInterface;
use App\Repositories\Google\GoogleInterface;
use App\Repositories\Passport\PassportInterface;
use App\Repositories\VerifyUser\VerifyUserInterface;

class MerchantService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING MERCHANT:';
    const LOGS_CREATED  = 'CREATED MERCHANT:';
    const LOGS_UPDATING = 'UPDATING MERCHANT:';
    const LOGS_UPDATED = 'UPDATED MERCHANT:';
    const LOGS_DELETING = 'DELETING MERCHANT:';
    const LOGS_DELETED = 'DELETED MERCHANT:';
    const LOGS_RETRIEVING = 'RETRIEVING MERCHANT:';
    const LOGS_RETRIEVED = 'RETRIEVED MERCHANT:';
    const LOGS_EXPORTING = 'EXPORTING MERCHANT:';
    const LOGS_IMPORTING = 'IMPORTING MERCHANT CSV:';
    const LOGS_LOGIN_ATTEMP = 'LOGIN ATTEMP:';
    const LOGS_LOGIN_FAILED = 'LOGIN FAILED:';
    const LOGS_LOGGED_IN = 'LOGGED IN:';
    const LOGS_EMAIL_VERIFICATION = 'VERIFYING EMAIL';
    const LOGS_EMAIL_VERIFICATION_FAILED = 'VERIFYING EMAIL FAILED';
    const LOGS_UPDATE_STATUS = 'UPDATING KYC STATUS';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-MERCHANT-001' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-MERCHANT-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_LOGIN_CREDENTIALS = array('code' => 'E-MERCHANT-003' , 'message' => 'Invalid username or password', 'http_code' => 400);
    const API_ERROR_EMAIL_VERIFICATION = array('code' => 'E-MERCHANT-004' , 'message' => 'Invalid verification link', 'http_code' => 400);
    const API_ERROR_EMAIL_VERIFICATION_VERIFIED = array('code' => 'E-MERCHANT-005' , 'message' => 'This link is already verified.', 'http_code' => 400);
    const API_ERROR_UNVERIFIED = array('code' => 'E-MERCHANT-006' , 'message' => 'Your account need verification', 'http_code' => 400);
    const API_ERROR_INVALID_CAPTCHA = array('code' => 'E-MERCHANT-010' , 'message' => 'Invalid captcha', 'http_code' => 400);
    const API_ERROR_NO_PERMISSION = array('code' => 'E-MERCHANT-011' , 'message' => 'No permission to access', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-MERCHANT-404' , 'message' => 'Merchant not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-MERCHANT-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-Merchants.xls';
    const EXPORT_HEADERS = ['Bank ID','Code','Name','Active Status','Date Updated','Date Created','Date Deleted'];

    /* IMPORT VARIABLES */
    const IMPORT_CSV_DELIMETER = ',';
    const ACCOUNT_ID_FORMAT = "%06d";

    /*DB FILTER FIELDS*/
    const DB_FILTER_FIELDS = ['account_id', 'first_name', 'last_name', 'email'];
    const DB_DATE_FORMAT = 'Y-m-d';

    const DB_FIELD_ACCOUNT_ID = 'account_id';
    const DB_FIELD_MERCHANT_ID = 'merchant_id';
    const DB_FIELD_AFFILIATION = 'affiliation';
    const DB_FIELD_TYPE = 'type';
    const DB_FIELD_BIRTHDATE = 'birthdate';
    const DB_FIELD_COUNTRY = 'country';
    const DB_FIELD_AGENT = 'agent';
    const DB_FIELD_STATE = 'state';
    const DB_FIELD_CITY = 'city';
    const DB_FIELD_STREET = 'street';
    const DB_FIELD_POSTAL = 'postal';
    const DB_FIELD_CONTACT_NUMBER = 'contact_number';

    const DB_FIELD_USER_ID = 'user_id';
    const DB_FIELD_TOKEN = 'token';
    const DB_FIELD_KYC_STATUS = 'kyc_status';

    const MERCHANT_ROLE = 'Merchant';
    const FRONTEND_PERMISSION = 'frontend';

    protected $merchantInterface;
    protected $userInterface;
    protected $passportInterface;
    protected $verifyUserInterface;
    protected $excelExport;
    protected $googleInterface;

    public function __construct(
        MerchantInterface $merchantInterface,
        UserInterface $userInterface,
        PassportInterface $passportInterface,
        VerifyUserInterface $verifyUserInterface,
        GoogleInterface $googleInterface,
        Excel $excelExport
   ) {
        $this->merchantInterface = $merchantInterface;
        $this->userInterface = $userInterface;
        $this->passportInterface = $passportInterface;
        $this->verifyUserInterface = $verifyUserInterface;
        $this->googleInterface = $googleInterface;
        $this->excelExport = $excelExport;
    }

    public function create(Request $request)
    {
        try {
            $validator = $this->validateCreateRequest($request);
            if ($this->validateReCaptcha($request)) {
                if ($validator->fails()) {
                    Log::info(self::LOGS_CREATING . ' - ' . json_encode($validator->errors()));
                    return $this->sendError(self::API_ERROR_INVALID_CREATE_REQUEST, $validator->errors());
                }
                $data = $request->all();
                if (isset($data[self::DB_FIELD_BIRTHDATE])) {
                    $data[self::DB_FIELD_BIRTHDATE] = date(self::DB_DATE_FORMAT, strtotime($data[self::DB_FIELD_BIRTHDATE]));
                }
                $data[self::DB_FIELD_ACCOUNT_ID] = $this->generateAccountId();
                $merchant = $this->merchantInterface->create($data);
                $user = $this->userInterface->create($data);
                $user->assignRole(self::MERCHANT_ROLE);

                $this->verifyUserInterface->create([
                    self::DB_FIELD_USER_ID => $user->id,
                    self::DB_FIELD_TOKEN => str_random(40)
                ]);

                Mail::to($request->email)->send(new NewRegisteredUserMailer($user));
                //Log::info(self::LOGS_CREATED . $request->user() . ' - ' . json_encode($merchant));
                return $this->sendResponse($merchant->getOriginal(), '');
            } else {
                Log::info(self::LOGS_CREATING . ' - ' . self::API_ERROR_INVALID_CAPTCHA['message']);
                return $this->sendError(self::API_ERROR_INVALID_CAPTCHA, '');
            }
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
            $merchant = $this->merchantInterface->update($id, $request->all());
            if ($merchant) {
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($merchant));
                return $this->sendResponse($merchant->getOriginal(), '');
            }
            return $this->sendError(self::API_ERROR_NOT_FOUND, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function updateKycStatus($id, Request $request)
    {
        try {
            Log::info(self::LOGS_UPDATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $payload = $request->all();
            $data = [self::DB_FIELD_KYC_STATUS => $request->status];
            $merchant = $this->merchantInterface->update($id, $data);
            if ($merchant) {
                Mail::to($merchant->email)->send(new KycUpdateMailer($merchant));
                Log::info(self::LOGS_UPDATED . $request->user() . ' - ' . json_encode($merchant));
                return $this->sendResponse($merchant->getOriginal(), '');
            }
            return $this->sendError(self::API_ERROR_NOT_FOUND, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_UPDATE_STATUS . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function delete($id, $request)
    {
        try {
            Log::info(self::LOGS_DELETING . $request->user()->email . ' - ' . $id);
            $merchant = $this->merchantInterface->delete($id);
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
            $merchant = $this->merchantInterface->get($id);
            if ($merchant) {
                return $this->sendResponse($merchant->getOriginal(), '');
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
            $filters = $this->getFilterParameters($request, self::DB_FILTER_FIELDS);
            $merchants = $this->merchantInterface->getAll($request, $filters);
            $data = $this->formatData($merchants);
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
            $merchants = $this->merchantInterface->getAll($request);
            return $this->excelExport->setData($merchants->toArray(), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function logIn(Request $request)
    {
        try {
            Log::info(self::LOGS_LOGIN_ATTEMP . ' - ' . json_encode($request->all()));
            $token = $this->getToken($request);
            if ($token) {
                $user = $this->userInterface->where(['email' => $request->email]);
                if ($token && $user[0]->active === 1) {
                    if ($user[0]->verified === 1) {
                        Log::info(self::LOGS_LOGGED_IN . ' - ' . json_encode($request->all()));
                        $user = $this->userInterface->where(['email' => $request->email]);
                        $permissions = $this->getUsersPermissions($user[0]);
                        $frontend = $this->hasFrontEndPermissions($user[0]);

                        if (!$frontend) {
                            Log::info(self::LOGS_LOGIN_FAILED . ' - ' . json_encode($request->all()));
                            return $this->sendError(self::API_ERROR_NO_PERMISSION, '');
                        }

                        $merchant = $this->merchantInterface->where(['email' => $request->email]);
                        $userData = $this->mergeData($user[0], $merchant[0]);

                        return $this->sendResponse(array_merge(json_decode((string) $token->getBody(), true), [
                            'user' => $userData,
                            'permissions' => $permissions,
                            'verified' => $merchant[0]->kyc_status
                        ]), '');
                    } else {
                        Log::info(self::LOGS_LOGIN_FAILED . ' - ' . json_encode($request->all()));
                        return $this->sendError(self::API_ERROR_UNVERIFIED, '');
                    }
                } else {
                    Log::info(self::LOGS_LOGIN_FAILED . ' - ' . json_encode($request->all()));
                    return $this->sendError(self::API_ERROR_INVALID_LOGIN_CREDENTIALS, '');
                }
            } else {
                Log::info(self::LOGS_LOGIN_FAILED . ' - ' . json_encode($request->all()));
                return $this->sendError(self::API_ERROR_INVALID_LOGIN_CREDENTIALS, '');
            }
        } catch (Exception $e) {
            Log::info(self::LOGS_LOGIN_FAILED . ' - ' . json_encode($request->all()));
            return $this->sendError(self::API_ERROR_UNEXPECTED, isset($e->message)? $e->message : $e->getMessage());
        }
    }

    public function verifyAccount($hash)
    {
        try {
            Log::info(self::LOGS_IMPORTING);
            $verify = $this->verifyUserInterface->getByHash($hash);
            if (isset($verify)) {
                if (!$verify->user->verified) {
                    $data = $this->verifyUserInterface->verify($hash);
                    Mail::to($verify->user->email)->send(new VerificationUserMailer($data));
                    return $this->sendResponse($data, '');
                } else {
                    Log::info(self::LOGS_EMAIL_VERIFICATION_FAILED . ' -  ' . $verify->user->email);
                    return $this->sendError(self::API_ERROR_EMAIL_VERIFICATION_VERIFIED, '');
                }
            } else {
                Log::info(self::LOGS_EMAIL_VERIFICATION_FAILED . ' - HASH: ' . $hash);
                return $this->sendError(self::API_ERROR_EMAIL_VERIFICATION, '');
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_IMPORTING . ' - ' . $message);
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

    private function validateReCaptcha($request)
    {
        return $this->googleInterface->validateReCaptcha($request->recaptcha);
    }

    private function validateCreateRequest($request)
    {
        $storeMerchantRequest = new StoreMerchantRequest();
        $rules = $storeMerchantRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function validateUpdateRequest($request)
    {
        $updateMerchantRequest = new UpdateMerchantRequest();
        $rules = $updateMerchantRequest->rules();
        return Validator::make($request->all(), $rules);
    }

    private function mergeData($user, $merchant)
    {
        $merchantData = $merchant->getOriginal();
        $userData = $user->getOriginal();
        $userData[self::DB_FIELD_MERCHANT_ID] = $merchantData['id'];
        $userData[self::DB_FIELD_ACCOUNT_ID] = $merchantData[self::DB_FIELD_ACCOUNT_ID];
        $userData[self::DB_FIELD_TYPE] = $merchantData[self::DB_FIELD_TYPE];
        $userData[self::DB_FIELD_BIRTHDATE] = $merchantData[self::DB_FIELD_BIRTHDATE];
        $userData[self::DB_FIELD_COUNTRY] = $merchantData[self::DB_FIELD_COUNTRY];
        $userData[self::DB_FIELD_AGENT] = $merchantData[self::DB_FIELD_AGENT];
        $userData[self::DB_FIELD_STATE] = $merchantData[self::DB_FIELD_STATE];
        $userData[self::DB_FIELD_CITY] = $merchantData[self::DB_FIELD_CITY];
        $userData[self::DB_FIELD_STREET] = $merchantData[self::DB_FIELD_STREET];
        $userData[self::DB_FIELD_POSTAL] = $merchantData[self::DB_FIELD_POSTAL];
        $userData[self::DB_FIELD_CONTACT_NUMBER] = $merchantData[self::DB_FIELD_CONTACT_NUMBER];
        $userData[self::DB_FIELD_KYC_STATUS] = $merchantData[self::DB_FIELD_KYC_STATUS];

        return $userData;
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

    private function getUsersPermissions($user)
    {
        $permissions = $user->getAllPermissions();
        $arrPermissions = array();
        foreach ($permissions as $permission) {
            $arrPermissions[] = $permission->name;
        }

        return $arrPermissions;
    }

    private function generateAccountId()
    {
        $init_id = sprintf(self::ACCOUNT_ID_FORMAT, mt_rand(1, 999999));
        $res = $this->merchantInterface->where([self::DB_FIELD_ACCOUNT_ID => $init_id]);
        if (!$res->count()) {
            return $init_id;
        }

        $this->generateAccountId();
    }

    protected function getFilterParameters($request, $filterFields)
    {
        $filters = array();
        $request = $request->all();
        foreach ($request as $key => $value) {
            if (in_array($key, $filterFields)) {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }

    private function getToken($request)
    {
        return $this->passportInterface->getToken($request);
    }

    private function hasFrontEndPermissions($user)
    {
        $permissions = $user->getAllPermissions();
        $arrPermissions = array();
        foreach ($permissions as $permission) {
            if ($permission->name == self::FRONTEND_PERMISSION) {
                return true;
            }
        }

        return false;
    }
}
