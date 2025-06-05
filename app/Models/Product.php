<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

    public const IMAGE_FOLDER = 'product';

    protected $fillable = [
        'name',
        'unit_id',
        'category_id',
        'quantity',
    ];

    public function shelterProducts()
    {
        return $this->hasMany(ShelterProduct::class);
    }

    public function productRequests()
    {
        return $this->hasMany(ProductRequest::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('products')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'parentable');
    }
}
