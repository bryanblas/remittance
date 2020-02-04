<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;

    protected $table = 'banks';

    /**
     * The attributes that are mass assignable.
     *
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'code',
        'name',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected static $recordEvents = ['updated'];
    protected static $logFillable = true;
    protected static $logOnlyDirty = 1;
    protected static $logName = 'Banks';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }
    public function BankAccounts()
    {
        return $this->hasMany('App\Models\BankAccount', 'bank_account_id', 'id');
    }
}
