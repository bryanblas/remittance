<?php

namespace App\Services\User;

use Auth;
use Exception;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\User\ForgotPasswordMailer;
use App\Mail\User\AddUserMailer;
use App\Exports\Excel;
use App\Services\BaseService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Repositories\User\UserInterface;
use App\Repositories\Google\GoogleInterface;
use App\Repositories\Passport\PassportInterface;
use App\Repositories\PasswordReset\PasswordResetInterface;
use Illuminate\Support\Facades\Hash;

class UserService extends BaseService
{
    /*LOGS*/
    const LOGS_CREATING = 'CREATING USER:';
    const LOGS_CREATED  = 'CREATED USER:';
    const LOGS_UPDATING = 'UPDATING USER:';
    const LOGS_UPDATED = 'UPDATED USER:';
    const LOGS_DELETING = 'DELETING USER:';
    const LOGS_DELETED = 'DELETED USER:';
    const LOGS_RETRIEVING = 'RETRIEVING USER:';
    const LOGS_RETRIEVED = 'RETRIEVED USER:';
    const LOGS_LOGIN_ATTEMP = 'LOGIN ATTEMP:';
    const LOGS_LOGIN_FAILED = 'LOGIN FAILED:';
    const LOGS_LOGGED_IN = 'LOGGED IN:';
    const LOGS_REGISTRATION = 'USER REGISTRATION:';
    const LOGS_LOGS_CHANGE_PASSWORD = 'CHANGE PASSWORD:';
    const LOGS_FORGOT_PASSWORD = 'FORGOT PASSWORD:';
    const LOGS_RESET_PASSWORD = 'FORGOT PASSWORD:';
    const LOGS_EXPORTING = 'EXPORTING USERS:';

    /*DB FIELDS*/
    const DB_DATE_FIELD = 'hash';
    const DB_EMAIL_FIELD = 'email';
    const DB_EXPIRY_FIELD = 'expiry';
    const DB_STATUS_FIELD = 'status';

    /*DB FILTER FIELDS*/
    const DB_FILTER_FIELDS = ['first_name','middle_name','last_name','status'];
    const DB_DEFAULT_ORDERING = 'created_at';
    const DB_DEFAULT_ORDER_DIRECTION = 'DESC';

    const API_ROLE_NAME_FIELD   = 'role';

    /*API ERROR RESPONSE*/
    const API_ERROR_INVALID_LOGIN_CREDENTIALS = array('code' => 'E-USER-001' , 'message' => 'Invalid username or password', 'http_code' => 400);
    const API_ERROR_INVALID_CREATE_REQUEST = array('code' => 'E-USER-002' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_UPDATE_REQUEST = array('code' => 'E-USER-003' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_CHANGE_PASSWORD_REQUEST = array('code' => 'E-USER-004' , 'message' => 'Invalid request', 'http_code' => 400);
    const API_ERROR_INVALID_OLD_PASSWORD = array('code' => 'E-USER-005' , 'message' => 'Invalid old password', 'http_code' => 400);
    const API_ERROR_INVALID_FORGOT_PASSWORD_REQUEST = array('code' => 'E-USER-006' , 'message' => 'Invalid forgot password request', 'http_code' => 400);
    const API_ERROR_INVALID_FORGOT_PASSWORD_EMAIL = array('code' => 'E-USER-007' , 'message' => 'Email address does not exist', 'http_code' => 400);
    const API_ERROR_INVALID_RESET_PASSWORD_HASH = array('code' => 'E-USER-008' , 'message' => 'This request has been canceled or expired', 'http_code' => 400);
    const API_ERROR_INVALID_RESET_PASSWORD_REQUEST = array('code' => 'E-USER-009' , 'message' => 'Invalid reset password request', 'http_code' => 400);
    const API_ERROR_INVALID_CAPTCHA = array('code' => 'E-USER-010' , 'message' => 'Invalid captcha', 'http_code' => 400);
    const API_ERROR_NO_PERMISSION = array('code' => 'E-USER-011' , 'message' => 'No permission to access admin', 'http_code' => 400);
    const API_ERROR_NOT_FOUND = array('code' => 'E-USER-404' , 'message' => 'Not found.', 'http_code' => 404 );
    const API_ERROR_UNEXPECTED = array('code' => 'E-USER-500' , 'message' => 'An unexpected error has occurred', 'http_code' => 500);

    /*EXPORT FILE NAME*/
    const EXPORT_FILE_NAME = 'PCSO-Users.xls';
    const EXPORT_HEADERS = ['User ID','Username/Email','First Name','Middle Name','Last Name','Outlet Code/Number','Outlet Name','Region PSGC','Region Name'];

    /*PERMISSIONS*/
    const PERMISSION_ALL_USERS = 'users_all';

    /*RESET PASSWORD*/
    const RESET_PASSWORD_URL = 'reset-password/';
    const RESET_PASSWORD_DEACTIVATE_URL = 'reset-password-deactivate/';
    const RESET_EMAIL_HASH_LENGTH = 64;

    const FRONTEND_PERMISSION = 'frontend';

    protected $userInterface;
    protected $googleInterface;
    protected $passwordResetInterface;
    protected $excelExport;
    protected $randomizer;

    public function __construct(
        UserInterface $userInterface,
        GoogleInterface $googleInterface,
        PassportInterface $passportInterface,
        PasswordResetInterface $passwordResetInterface,
        Excel $excelExport,
        Str $randomizer
   ) {
        $this->userInterface = $userInterface;
        $this->googleInterface = $googleInterface;
        $this->passportInterface = $passportInterface;
        $this->passwordResetInterface = $passwordResetInterface;
        $this->excelExport = $excelExport;
        $this->randomizer = $randomizer;
    }

    public function create($request)
    {
        try {
            Log::info(self::LOGS_CREATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $validator = $this->validateCreateRequest($request);
            if ($validator->fails()) {
                Log::info(self::LOGS_CREATING . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_CREATE_REQUEST, $validator->errors());
            }
            DB::beginTransaction();
            $user = $this->userInterface->create($request->all());
            $user->assignRole($request['role']);
            DB::commit();
            //Mail::to($request->email)->send(new AddUserMailer(array('email' => $request->email, 'password' => $request->password), env('UI_APP_URL')));
            Log::info(self::LOGS_CREATED . ' - ' . json_encode($request->all()));
            return $this->sendResponse($user->getOriginal(), '');
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_CREATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function update($id, $request)
    {
        try {
            Log::info(self::LOGS_UPDATING . $request->user()->email . ' - ' . json_encode($request->all()));
            $validator = $this->validateUpdateRequest($id, $request);
            if ($validator->fails()) {
                Log::info(self::LOGS_UPDATING . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_UPDATE_REQUEST, $validator->errors());
            }
            DB::beginTransaction();
            $user = $this->userInterface->update($id, $request->all());
            $user->assignRole($request['role']);
            DB::commit();
            Log::info(self::LOGS_UPDATED . ' - ' . json_encode($request->all()));
            return $this->sendResponse($this->formatData($user), '');
        } catch (Exception $e) {
            DB::rollBack();
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_UPDATING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function get($id)
    {
        try {
            Log::info(self::LOGS_RETRIEVING . ' - ' . $id);
            $user = $this->userInterface->get($id);
            if ($user) {
                $data = $this->formatData($user);
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
            $users = $this->userInterface->getAll($filters, $orderBy, $orderDirection, $perPage, $page);
            $data = $this->formatData($users);
            return $this->sendResponse($data, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RETRIEVING . ' - ' . $message);
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
                    Log::info(self::LOGS_LOGGED_IN . ' - ' . json_encode($request->all()));
                    $user = $this->userInterface->where(['email' => $request->email]);

                    $permissions = $this->getUsersPermissions($user[0]);

                    if (!$permissions) {
                        Log::info(self::LOGS_LOGIN_FAILED . ' - ' . json_encode($request->all()));
                        return $this->sendError(self::API_ERROR_NO_PERMISSION, '');
                    }

                    return $this->sendResponse(array_merge(json_decode((string) $token->getBody(), true), [
                        'user' =>$this->formatData($user[0]),
                        'permissions' => $permissions
                    ]), '');
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

    public function register($request)
    {
        try {
            Log::info(self::LOGS_REGISTRATION . ' - ' . json_encode($request->all()));
            $validator = $this->validateCreateRequest($request);
            if ($this->validateReCaptcha($request)) {
                if ($validator->fails()) {
                    Log::info(self::LOGS_REGISTRATION . ' - ' . json_encode($validator->errors()));
                    return $this->sendError(self::API_ERROR_INVALID_CREATE_REQUEST, $validator->errors());
                }
                $user = $this->userInterface->create($request->all());
                if ($user) {
                    $user->assignRole($request['role']);
                    return $this->logIn($request);
                }
            } else {
                Log::info(self::LOGS_REGISTRATION . ' - ' . self::API_ERROR_INVALID_CAPTCHA['message']);
                return $this->sendError(self::API_ERROR_INVALID_CAPTCHA, '');
            }
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_REGISTRATION . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function delete($id, $request)
    {
        try {
            Log::info(self::LOGS_DELETING . ' - ' . json_encode($request->all()));
            $result =  $this->userInterface->delete($id);
            Log::info(self::LOGS_DELETED . $request->user()->email . ' - ' . json_encode($request->all()));
            return $this->sendResponse('', '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_DELETING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function changePassword($request)
    {
        try {
            Log::info(self::LOGS_LOGS_CHANGE_PASSWORD . $request->user()->email . ' - ' . json_encode($request->all()));
            $validator = $this->validateChangePasswordRequest($request);
            if ($validator->fails()) {
                Log::info(self::LOGS_LOGS_CHANGE_PASSWORD . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_CHANGE_PASSWORD_REQUEST, $validator->errors());
            }
            $user =  Auth::guard('web')->attempt(['email' => $request->user()->email, 'password' => $request->old_password]);
            if (!$user) {
                Log::info(self::LOGS_LOGS_CHANGE_PASSWORD . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_OLD_PASSWORD, $validator->errors());
            }
            $user =  $this->userInterface->update($request->user()->id, $request->all());
            Log::info(self::LOGS_LOGS_CHANGE_PASSWORD . $request->user()->email . ' - ' . json_encode($request->all()));
            return $this->sendResponse($user, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_LOGS_CHANGE_PASSWORD . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function forgotPassword($request)
    {
        try {
            Log::info(self::LOGS_FORGOT_PASSWORD . ' - ' . json_encode($request->email));
            $validator = $this->validateForgotPasswordRequest($request);
            if ($validator->fails()) {
                Log::info(self::LOGS_FORGOT_PASSWORD . ' - ' . json_encode($validator->errors()));
                return $this->sendError(self::API_ERROR_INVALID_FORGOT_PASSWORD_REQUEST, $validator->errors());
            }
            if ($this->validateReCaptcha($request)) {
                $users = $this->userInterface->where(['email' => $request->email]);
                if (isset($users[0])) {
                    $hash = $this->randomizer::random(self::RESET_EMAIL_HASH_LENGTH);
                    $this->savePasswordResetDetails($request, $hash);
                    $resetPasswordLink = $this->getResetPasswordLink($hash);
                    $resetPasswordLinkDeActivate = $this->getResetPasswordDeactivationLink($hash);
                    $result = Mail::to($request->email)->send(new ForgotPasswordMailer($resetPasswordLink, $resetPasswordLinkDeActivate));
                    Log::info(self::LOGS_FORGOT_PASSWORD . $request->email . ' - ' . $resetPasswordLink);
                    return $this->sendResponse('', '');
                }
                Log::info(self::LOGS_FORGOT_PASSWORD . $request->email . ' - ' . self::API_ERROR_INVALID_FORGOT_PASSWORD_EMAIL['message']);
                return $this->sendError(self::API_ERROR_INVALID_FORGOT_PASSWORD_EMAIL, '');
            }
            Log::info(self::LOGS_REGISTRATION . ' - ' . self::API_ERROR_INVALID_CAPTCHA['message']);
            return $this->sendError(self::API_ERROR_INVALID_CAPTCHA, '');
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_FORGOT_PASSWORD . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function checkResetPasswordCredentials($hash)
    {
        $result = $this->checkResetPasswordHash($hash);
        if ($result === true) {
            return $this->sendResponse('', '');
        }
        return $result;
    }

    public function resetPassword($hash, $request)
    {
        try {
            $result = $this->checkResetPasswordHash($hash);
            $result = true;
            if ($result === true) {
                Log::info(self::LOGS_RESET_PASSWORD . $hash . ' - ' . json_encode($request->all()));
                $validator = $this->validateResetPasswordRequest($request);
                if ($validator->fails()) {
                    Log::info(self::LOGS_RESET_PASSWORD . ' - ' . json_encode($validator->errors()));
                    return $this->sendError(self::API_ERROR_INVALID_RESET_PASSWORD_REQUEST, $validator->errors());
                }
                $reset = $this->passwordResetInterface->get($hash);
                if (!$reset) {
                    Log::info(self::LOGS_RESET_PASSWORD . ' - ' . json_encode($validator->errors()));
                    return $this->sendError(self::API_ERROR_NOT_FOUND, $validator->errors());
                }
                $user = $this->userInterface->where(['email' => $reset->email]);
                $user =  $this->userInterface->update($user[0]->id, ['password' => $request->password]);
                $this->passwordResetInterface->update($hash, ['status' => 0]);
                Log::info(self::LOGS_RESET_PASSWORD . $user->email . ' - ' . json_encode($request->all()));
                return $this->sendResponse('', '');
            }
            return $result;
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_RESET_PASSWORD . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    public function export($request)
    {
        try {
            Log::info(self::LOGS_EXPORTING);
            $orderBy = isset($request['order_by']) ? $request['order_by']: self::DB_DEFAULT_ORDERING;
            $orderDirection = isset($request['order_direction']) ? $request['order_direction']: self::DB_DEFAULT_ORDER_DIRECTION;
            $filters = $this->getFilterParamters($request, self::DB_FILTER_FIELDS);
            $users = $this->userInterface->getAll($filters, $orderBy, $orderDirection, false, false);
            return $this->excelExport->setData($this->formatExportData($users), self::EXPORT_HEADERS)->download(self::EXPORT_FILE_NAME);
        } catch (Exception $e) {
            $message = $this->getErrorMessage($e);
            Log::info(self::LOGS_EXPORTING . ' - ' . $message);
            return $this->sendError(self::API_ERROR_UNEXPECTED, $message);
        }
    }

    protected function getFilterParamters($request, $filterFields)
    {
        $filters = array();
        $user = $request->user();
        $request = $request->all();
        foreach ($request as $key => $value) {
            if (in_array($key, $filterFields)) {
                $filters[$key] = $value;
            }
        }

        return $filters;
    }

    private function checkResetPasswordHash($hash)
    {
        $passwordReset = $this->passwordResetInterface->get($hash);
        if ($passwordReset) {
            if ($passwordReset->status === 1 && !$this->hasResetPasswordExpired($passwordReset->expiry)) {
                return true;
            }
            return $this->sendError(self:: API_ERROR_INVALID_RESET_PASSWORD_HASH, '');
        }
        return $this->sendError(self::API_ERROR_NOT_FOUND, '');
    }

    protected function addExtraData($users)
    {
        $userList = array();
        foreach ($users as $user) {
            $userData = $user->getOriginal();
            $roles = $user->roles->pluck('name');
            $userData[self::API_ROLE_NAME_FIELD] = isset($roles[0])? $roles[0]: '';
            $userList[] = $userData;
        }
        return $userList;
    }

    private function formatExportData($users)
    {
        $exportList = array();
        foreach ($users as $user) {
            $exportList[] = [
                $user->id,
                $user->email,
                $user->first_name,
                $user->middle_name,
                $user->last_name
            ];
        }
        return $exportList;
    }

    private function getUsersPermissions($user)
    {
        $permissions = $user->getAllPermissions();
        $arrPermissions = array();
        foreach ($permissions as $permission) {
            $arrPermissions[] = $permission->name;
        }

        if (in_array(self::FRONTEND_PERMISSION, $arrPermissions)) {
            return false;
        }

        return $arrPermissions;
    }

    private function validateCreateRequest($request)
    {
        $storeUserRequest = new StoreUserRequest();
        $rules = $storeUserRequest->rules();
        $validator = Validator::make($request->all(), $rules);
        return $validator;
    }

    private function validateUpdateRequest($id, $request)
    {
        $updateUserRequest = new UpdateUserRequest();
        $rules = $updateUserRequest->rules($id);
        $validator = Validator::make($request->all(), $rules);
        return $validator;
    }

    private function validateChangePasswordRequest($request)
    {
        $changePasswordRequest = new ChangePasswordRequest();
        $rules = $changePasswordRequest->rules();
        $validator = Validator::make($request->all(), $rules);
        return $validator;
    }

    private function validateForgotPasswordRequest($request)
    {
        $forgotPasswordRequest = new ForgotPasswordRequest();
        $rules = $forgotPasswordRequest->rules();
        $validator = Validator::make($request->all(), $rules);
        return $validator;
    }

    private function validateResetPasswordRequest($request)
    {
        $resetPasswordRequest = new ResetPasswordRequest();
        $rules = $resetPasswordRequest->rules();
        $validator = Validator::make($request->all(), $rules);
        return $validator;
    }

    private function validateReCaptcha($request)
    {
        return $this->googleInterface->validateReCaptcha($request->recaptcha);
    }

    private function getToken($request)
    {
        return $this->passportInterface->getToken($request);
    }

    private function savePasswordResetDetails($request, $hash)
    {
        return $this->passwordResetInterface->create([
            self::DB_DATE_FIELD => $hash,
            self::DB_EMAIL_FIELD => $request->email,
            self::DB_EXPIRY_FIELD => Carbon::now()->addMinutes(env('PASSWORD_RESET_EXPIRY')),
            self::DB_STATUS_FIELD => 1
        ]);
    }

    private function hasResetPasswordExpired($expiry)
    {
        $startTime = Carbon::now();
        $endTime = Carbon::parse($expiry);
        $timeRemaining = $startTime->diffInSeconds($endTime, false);
        if ($timeRemaining <= 0) {
            return true;
        }
        return false;
    }

    private function getResetPasswordLink($hash)
    {
        return env('UI_APP_URL') .'/' .self::RESET_PASSWORD_URL  . $hash;
    }

    private function getResetPasswordDeactivationLink($hash)
    {
        return env('UI_APP_URL') .'/' .self::RESET_PASSWORD_DEACTIVATE_URL  . $hash;
    }
}
