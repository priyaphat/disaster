<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VulnerableGroup extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    protected $fillable = [
        'name',
    ];

    public function populations(): BelongsTo
    {
        return $this->belongsTo(Population::class, 'id', 'vulnerable_group_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Disaster')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
