<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'id',
        'log_name',
        'description',
        'causer_id',
    ];

    protected $table = 'activity_log';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'causer_id');
    }
}
