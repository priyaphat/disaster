<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class AppointmentLog extends Model
{
    //
    protected $fillable = ['appointment_id', 'parentable_id', 'parentable_type', 'description', 'log_date'];
    protected $casts = [
        'description' => 'array'
    ];

    public function parent()
    {
        return $this->morphTo('parentable');  // เชื่อมกับ parentable_id, parentable_type
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('appointment_logs')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
