<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ShelterProduct extends Model
{
    use LogsActivity;

    public const PLUS = 1;
    public const MINUS = -1;
    public const Shelter_center = 0;

    protected $fillable = [
        'product_id',
        'quantity',
        'shelter_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function shelter()
    {
        return $this->belongsTo(Shelter::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('shelter_products')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
