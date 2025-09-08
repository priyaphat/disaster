<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Traits\LogsActivity;

class DisasterEarthquake extends Model
{
    use CreatedUpdatedBy, LogsActivity;
    protected $fillable = [
        'address',
        'latitude',
        'longitude',
        'depth',
        'magnitude',
        'occurrence_time',
        'geography_id',
        'line_oa',
        'created_by',
        'updated_by'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('DisasterEarthquake')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function area(): HasOne
    {
        return $this->hasOne(Geography::class, 'id', 'geography_id');
    }
}

// INSERT INTO earthquakes (
//   address, latitude, longitude, depth, magnitude,
//   occurrence_time, line_oa, created_by, updated_by,
//   created_at, updated_at
// ) VALUES
// (
//   'เชียงราย ประเทศไทย', 19.9100, 99.8400, 10.5, 4.2,
//   '2025-06-06 02:30:00', 1, 2, 2,
//   '2025-06-06 03:00:00', '2025-06-06 03:00:00'
// ),
// (
//   'ลำปาง ประเทศไทย', 18.2900, 99.4900, 7.8, 3.9,
//   '2025-06-05 22:15:00', 0, 2, 2,
//   '2025-06-06 00:00:00', '2025-06-06 00:00:00'
// ),
// (
//   'แม่ฮ่องสอน ประเทศไทย', 19.3000, 97.9700, 12.0, 5.0,
//   '2025-06-04 14:45:00', 1, 2, 2,
//   '2025-06-04 15:00:00', '2025-06-04 15:00:00'
// );
