<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SystemSetting extends Model
{
    use LogsActivity;

    protected $fillable = [
        'id',
        'property_name',
        'description',
        'value',
    ];

    protected static $recordEvents = ['updated'];
    protected static $logFillable = true;
    protected static $logOnlyDirty = 1;
    protected static $logName = 'System Setting';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }
}
