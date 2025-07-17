<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Http\Traits\CreatedUpdatedBy;


class EmergencyPost extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    public const inactive = 0;
    public const active = 1;

    //log
    public const create = 1;
    public const update = 2;
    public const delete = 3;

    protected $fillable = [
        'disaster_id',
        'level',
        'province_id',
        'gis_map',
        'latitude',
        'longitude',
        'status',
        'line_oa',
        // 'history_id',
    ];

    public function areas()
    {
        return $this->hasMany(RefArea::class, 'post_id')
            ->join('geography', 'ref_areas.district_id', '=', 'geography.district_code')
            ->distinct()
            ->select('ref_areas.*', 'geography.district_name_th as district_name');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('EmergencyPost')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
