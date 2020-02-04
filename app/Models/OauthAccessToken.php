<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OauthAccessToken extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'id',
        'user_id',
        'client_id',
        'name',
        'scopes',
        'revoked'
    ];
}
