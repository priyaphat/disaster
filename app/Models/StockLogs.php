<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class StockLogs extends Model
{
    use CreatedUpdatedBy;

    public const INSTOCK = 1;
    public const OUTSTOCK = 2;
    public const TRANSFERIN = 3;
    public const TRANSFEROUT = 4;

    protected $casts = [
        'detail' => "array",
    ];

    protected $fillable = [
        'product_id',
        'detail',
        'amount',
        'total',
        'type',
        'shelter_id',
    ];
}
