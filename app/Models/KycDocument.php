<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KycDocument extends Model
{
    use SoftDeletes;

    protected $table = 'kyc_documents';

    /**
     * The attributes that are mass assignable.
     *
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'merchant_id',
        'document_type',
        'type',
        'filename',
        'status',
        'filesize',
        'remarks'
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

    public function Merchant()
    {
        return $this->belongsTo('App\Models\Merchant', 'merchant_id', 'id');
    }
}
