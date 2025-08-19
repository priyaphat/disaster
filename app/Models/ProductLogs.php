<?php

namespace App\Models;

use App\Enums\ProductLogs as EnumsProductLogs;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;

class   ProductLogs extends Model
{

    use CreatedUpdatedBy;

    public const PRODUCT = 1;
    public const REQUEST = 2;
    public const STOP = 3;
    public const RECEIVE = 4;

    protected $casts = [
        'detail' => "array",
        'type' => EnumsProductLogs::class,
    ];

    protected $fillable = [
        'from',
        'to',
        'detail',
        'type',
        'name',
        'category_id',
        'unit_id',
    ];
}
