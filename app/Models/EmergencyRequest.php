<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmergencyRequest extends Model
{
    use CreatedUpdatedBy,LogsActivity;

    protected $fillable = [
        'prefix_id',
        'first_name',
        'last_name',
        'phone',
        'range_age',
        'victim_name',
        'disaster_id',
        'description',
        'tambon_id',
        'address',
        'status',
        'emergency_team_id'
    ];

    public function prefix()
    {
        return $this->belongsTo(Prefix::class)->select(['id', 'name']);
    }

    public function disaster()
    {
        return $this->belongsTo(Disaster::class)->select(['id', 'name']);
    }

    public function reason(){
        return $this->hasOne(EmergencyRequestLog::class)->latestOfMany();
    }
    // public function emergencyTeam()
    // {
    //     return $this->belongsTo(EmergencyTeam::class);
    // }

        public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('EmergencyRequest')
        ->logOnly(['*'])
        ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

}
