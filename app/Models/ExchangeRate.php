<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'currency_from',
        'currency_to',
        'rate',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected static $recordEvents = ['updated'];
    protected static $logFillable = true;
    protected static $logOnlyDirty = 1;
    protected static $logName = 'Exchange Rates';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }
}
