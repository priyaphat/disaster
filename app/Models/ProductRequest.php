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
    protected $appends = ['image_path'];
    protected $hidden = ['product'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('product_requests')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }

    public function getImagePathAttribute()
    {
        return $this->product?->image?->image_path ?? null;
    }
}
