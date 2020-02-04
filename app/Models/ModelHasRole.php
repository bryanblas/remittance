<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'role_id',
        'model_type',
        'model_id',
    ];
}
