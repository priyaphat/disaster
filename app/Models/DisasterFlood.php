<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DisasterFlood extends Model
{
    use CreatedUpdatedBy, LogsActivity;
    protected $fillable = [
        'address',
        'water_source',
        'water_source_type',
        'status',
        'water_value',
        'geography_id',
        'host',
        'line_oa',
    ];

    protected $hidden = [
        'geography_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('DisasterFlood')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }


    public function area(): HasOne
    {
        return $this->hasOne(Geography::class, 'id', 'geography_id');
    }
}

// INSERT INTO water_floods (id, address, water_source, water_source_type, status, water_value, line_oa)
// VALUES
// (1, 'ตำบลบางรัก, อำเภอเมือง, กรุงเทพ', 'แม่น้ำเจ้าพระยา', 'แม่น้ำ', 3, 250, 1),
// (2, 'ตำบลหนองบัว, อำเภอบางเลน, นครปฐม', 'คลองบางเลน', 'คลอง', 2, 80, 0),
// (3, 'ตำบลท่ามะกา, อำเภอท่ามะกา, กาญจนบุรี', 'อ่างเก็บน้ำท่ามะกา', 'อ่างเก็บน้ำ', 5, 600, 1);
