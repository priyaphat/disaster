<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Disaster extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    //pointer
    public const DISASTER = 1; 
    public const EMERGENCYHELP = 2;

    protected $fillable = [
        'name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Disaster')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

}
