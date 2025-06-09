<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Shelter extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    public const IMAGE_FOLDER = 'shelter';
    public const OPEN = 1;
    public const CLOSED = 0;

    protected $fillable = [
        'name',
        'phone',
        'created_date',
        'status',
        'leader_id',
        'utilities',
        'address',
        'max_capacity',
    ];

    protected $casts = [
        'status' => 'integer',
        'utilities' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('shelters')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }
}
