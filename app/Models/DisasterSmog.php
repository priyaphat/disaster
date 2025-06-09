<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
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
        "line_oa"
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('DisasterSmog')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}

// INSERT INTO disaster_smogs (source, address, status, smog_value, line_oa)
// VALUES 
// ('กรมควบคุมมลพิษ', 'เชียงใหม่', 1, 85, 1),
// ('อาสาสมัคร', 'ลำพูน', 0, 45, 0),
// ('ระบบอัตโนมัติ', 'เชียงราย', 1, 103, 1);
