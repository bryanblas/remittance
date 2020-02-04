<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

use Hash;

class Merchant extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'merchants';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'account_id',
        'affiliation',
        'type',
        'country',
        'birthdate',
        'agent',
        'state',
        'city',
        'street',
        'postal',
        'contact_number',
        'active',
        'kyc_status'
    ];

    protected static $recordEvents = ['updated'];
    protected static $logFillable = true;
    protected static $logOnlyDirty = 1;
    protected static $logName = 'Merchant';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }
}
