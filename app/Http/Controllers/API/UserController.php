<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\User\UserFacade;

class UserController extends Controller
{
    public function __construct(UserFacade $user)
    {
        $this->user = $user;
    }

    public function create(Request $request)
    {
        return $this->user::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->user::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->user::delete($id, $request);
    }

    public function get($id)
    {
        return $this->user::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->user::getAll($request);
    }

    public function register(Request $request)
    {
        return $this->user::register($request);
    }

    public function logIn(Request $request)
    {
        return $this->user::logIn($request);
    }

    public function changePassword(Request $request)
    {
        return $this->user::changePassword($request);
    }

    public function forgotPassword(Request $request)
    {
        return $this->user::forgotPassword($request);
    }

    public function checkResetPasswordCredentials($hash)
    {
        return $this->user::checkResetPasswordCredentials($hash);
    }

    public function resetPassword(Request $request, $hash)
    {
        return $this->user::resetPassword($hash, $request);
    }

    public function export(Request $request)
    {
        return $this->user::export($request);
    }
}
