<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Http\Traits\CreatedUpdatedBy;


class EmergencyRequestLog extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    protected $fillable = [
        'emergency_request_id',
        'reason_id',
        'reason',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Emergency_request_log')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
