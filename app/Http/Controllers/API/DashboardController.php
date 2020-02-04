<?php

namespace App\Http\Controllers\API;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
    }

    public function index()
    {
        $user = Auth::user();
        $permissions = $user->getAllPermissions();
        $arrPermissions = array();
        foreach ($permissions as $permission) {
            $arrPermissions[] = $permission->name;
        }

        var_dump($arrPermissions);
        die;
    }
}
