<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $table = 'bank_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'type',
        'bank_id',
        'bank_branch',
        'bank_address1',
        'bank_address2',
        'bank_address3',
        'number',
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
    protected static $logName = 'Bank Accounts';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }
    public function Bank()
    {
        return $this->belongsTo('App\Models\Bank', 'bank_id', 'id');
    }
}
