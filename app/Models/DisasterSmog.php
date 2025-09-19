<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DisasterSmog extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    protected $fillable = [
        "source",
        "address",
        "status",
        "smog_value",
        "geography_id",
        'host',
        "line_oa"
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('DisasterSmog')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function area(): HasOne
    {
        return $this->hasOne(Geography::class, 'id', 'geography_id');
    }
}

// INSERT INTO disaster_smogs (source, address, status, smog_value, line_oa)
// VALUES 
// ('กรมควบคุมมลพิษ', 'เชียงใหม่', 1, 85, 1),
// ('อาสาสมัคร', 'ลำพูน', 0, 45, 0),
// ('ระบบอัตโนมัติ', 'เชียงราย', 1, 103, 1);
