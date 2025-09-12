<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Assessments extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    public const Assessment = 1;
    public const InspectionVisit = 2;
    public const InspectionChild = 3;

    protected $casts = [
        'detail' => "array"
    ];

    protected $fillable = [
        'population_id',
        'type',
        'name',
        'detail',
        'district_id',
        'datetime',
        'team_id',
        'team_name',
        'count',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Assessment')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
