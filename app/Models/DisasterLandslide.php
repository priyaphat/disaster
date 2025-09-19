<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DisasterLandslide extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    public const FILE = 'landslides';
    protected $fillable = [
        'name',
        'file_name',
        'file_id',
        'link',
        'host',
        'line_oa',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('DisasterLandslide')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
