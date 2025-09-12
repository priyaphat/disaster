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
    public const gis = 'https://shingeometics.com/webgis/WebMapCRI/disasterarea/index.php?amp=';

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
        'district_code',
        'status',
        'line_oa',
        'created_at',
        'updated_at'
        // 'history_id',
    ];

    public function disaster()
    {
        return $this->hasOne(Disaster::class, 'id', 'disaster_id')
            ->select('id', 'name');
    }

    // public function area()
    // {
    //     return $this->hasMany(RefArea::class, 'post_id')
    //         ->join('geography', 'ref_areas.district_id', '=', 'geography.district_code')
    //         ->distinct()
    //         ->select('ref_areas.*', 'geography.district_name_th as district_name')
    //         ->orderBy('ref_areas.district_id', 'asc');
    // }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('EmergencyPost')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
