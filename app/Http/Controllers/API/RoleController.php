<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Role\RoleFacade;

class RoleController extends Controller
{
    public function __construct(RoleFacade $role)
    {
        $this->role = $role;
    }

    public function create(Request $request)
    {
        return $this->role::create($request);
    }

    public function update(Request $request, $id)
    {
        return $this->role::update($id, $request);
    }

    public function delete(Request $request, $id)
    {
        return $this->role::delete($id, $request);
    }

    public function get($id)
    {
        return $this->role::get($id);
    }

    public function getAll(Request $request)
    {
        return $this->role::getAll($request);
    }
}
