<?php

namespace App\Models;

use App\Enums\ProductLogs as EnumsProductLogs;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductLogs extends Model
{

    use CreatedUpdatedBy;

    public const PRODUCT = 1;
    public const REQUEST = 2;
    public const STOP = 3;
    public const RECEIVE = 4;
    public const UPDATE = 5;

    protected $casts = [
        'detail' => "array",
        'type' => EnumsProductLogs::class,
    ];

    protected $fillable = [
        'product_request_id',
        'from',
        'to',
        'detail',
        'type',
        'name',
        'category_id',
        'unit_id',
        'group_id',
    ];

    public function fromShelter(): HasOne
    {
        return $this->hasOne(Shelter::class, 'id', 'from');
    }

    public function toShelter(): HasOne
    {
        return $this->hasOne(Shelter::class, 'id', 'to');
    }
}
