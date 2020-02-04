<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Model
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
        'transaction_date',
        'merchant_id',
        'user_id',
        'bank_account_id',
        'currency',
        'amount',
        'fee',
        'route',
        'deposit_slip',
        'deposit_type',
        'filename',
        'message',
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
    protected static $logName = 'Deposit';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }

    public function BankAccount()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bank_account_id', 'id');
    }

    public function Merchant()
    {
        return $this->belongsTo('App\Models\Merchant', 'merchant_id', 'id');
    }
}
