<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;

class DamagedUtilityLogs extends Model
{
    use CreatedUpdatedBy;

    public const created = 1;
    public const updated = 2;
    public const deleted = 3;

    protected $casts = [
        'datail' => "array"
    ];

    protected $fillable = [
        'type',
        'status',
        'datail',
    ];
}
