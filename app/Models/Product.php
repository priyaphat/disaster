<?php

namespace App\Models;

use App\Filters\ProductFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

    public const IMAGE_FOLDER = 'product';
    public const Active = 1;
    public const Inactive = 0;
    public const Not_unit = 1;
    public const Is_unit = 2;

    protected $fillable = [
        'name',
        'unit_id',
        'category_id',
        'group_id',
        'detail',
        'source',
        'unit_type',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new ProductFilter($filters))->apply($query);
    }
}
