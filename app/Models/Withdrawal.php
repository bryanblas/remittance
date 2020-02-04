<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Withdrawal extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'transaction_number',
        'merchant_id',
        'merchant_balance_id',
        'account_number',
        'beneficiary_name',
        'account_type',
        'currency',
        'amount',
        'rate',
        'fee',
        'beneficiary_country',
        'beneficiary_address',
        'bank_name',
        'swift_code',
        'bank_country',
        'bank_address',
        'contact_number',
        'remarks',
        'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected static $recordEvents = ['updated'];
    protected static $logFillable = true;
    protected static $logOnlyDirty = 1;
    protected static $logName = 'Withdrawal';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }

    public function Merchant()
    {
        return $this->belongsTo('App\Models\Merchant', 'merchant_id', 'id');
    }

    public function Balance()
    {
        return $this->belongsTo('App\Models\Balance', 'merchant_balance_id', 'id');
    }
}
