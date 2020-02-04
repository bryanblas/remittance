<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'merchant_id',
        'currency',
        'amount',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected static $recordEvents = ['updated'];
    protected static $logFillable = true;
    protected static $logOnlyDirty = 1;
    protected static $logName = 'Balances';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }
    public function Merchant()
    {
        return $this->hasMany('App\Models\Merchant', 'merchant_id', 'id');
    }
}
