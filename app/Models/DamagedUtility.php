<?php

namespace App\Models;

use App\Enums\DamagedUtility as EnumsDamagedUtility;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DamagedUtility extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    protected $casts = [
        'type' => EnumsDamagedUtility::class,
    ];
    protected $appends = ['type_name'];

    protected $fillable = [
        'name',
        'type'
    ];

    protected $hidden = ['created_by', 'updated_by', 'created_at', 'updated_at'];

    public function getTypeNameAttribute(): string
    {
        return $this->type?->label() ?? null;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('DamagedUtility')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
