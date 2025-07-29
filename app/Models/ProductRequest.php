<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Http\Traits\CreatedUpdatedBy;

class ProductRequest extends Model
{
    use LogsActivity, CreatedUpdatedBy;

    public const STOP = 0;
    public const ACTIVE = 1;
    public const NOT_QUANTITY = -1;

    protected $fillable = [
        'product_id',
        'shelter_id',
        'status',
        'quantity',
        'request',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('product_requests')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
