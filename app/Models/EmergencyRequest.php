<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmergencyRequest extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    public const waiting = 1;
    public const approve = 2; //รอความช่วยเหลือ
    public const disapprove = 3; //ปฏิเสธการช่วยเหลือ
    public const helping = 4;
    public const complete = 5;
    public const incomplete = 6;
    public const hospital = 7; //ส่ง ตัวไป รพ นอก


    protected $fillable = [
        'prefix_id',
        'first_name',
        'last_name',
        'phone',
        'range_age',
        'victim_name',
        'disaster_id',
        'description',
        'district_id',
        'subdistrict_id',
        'address',
        'status',
        'emergency_team_name',
        'assigner',
        'accept_by',
        'other',
        'hospital_id'
    ];

    public function prefix()
    {
        return $this->belongsTo(Prefix::class)->select(['id', 'name']);
    }

    public function disaster()
    {
        return $this->belongsTo(Disaster::class)->select(['id', 'name']);
    }

    public function reason()
    {
        return $this->hasOne(EmergencyRequestLog::class)->latestOfMany();
    }

    public function emergencyTeams()
    {
        return $this->hasMany(EmergencyTeam::class, 'emc_id', 'id');
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

    public function geography()
    {
        return $this->belongsTo(Geography::class, 'subdistrict_id', 'subdistrict_code');
    }
}
