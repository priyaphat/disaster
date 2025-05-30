<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProductRequest extends Model
{
    use LogsActivity;

    protected $fillable = [
        'product_id',
        'quantity',
        'shelter_id',
        'status',
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
}
