<?php

namespace App\Models;

use App\Filters\ProductRequestFilter;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductRequest extends Model
{
    use LogsActivity;

    public const CANCELED = 0;
    public const PENDING = 1;
    public const APPROVED = 2;

    public const NO_QUANTITY = 1;

    protected $fillable = [
        'product_id',
        'quantity',
        'no_quantity',
        'shelter_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'integer',  
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shelter()
    {
        return $this->belongsTo(Shelter::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('product_requests')
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
        return (new ProductRequestFilter($filters))->apply($query);
    }
}
