<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmittedOutletRequirement extends Model
{
    use SoftDeletes;

    protected $table = 'submitted_outlet_requirements';

    protected $with = array('reviews');

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'outlet_requirements_id',
        'outlet_id',
        'filename',
        'state',
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
    protected static $logName = 'Outlet';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Record has been {$eventName} for : ";
    }

    public function reviews()
    {
        return $this->hasMany('App\Models\Review', 'relation_id', 'id')->where('type', '=', 2)->orderBy('id', 'DESC');
    }
}
