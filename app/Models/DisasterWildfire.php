<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Support\Optional;

class DisasterWildfire extends Model
{

    use CreatedUpdatedBy;

    public const MODELI = 1;
    public const MODELII = 2;
    public const MODELIII = 3;
    public const MODELIV = 4;

    protected $casts = [
        'model' => "array",
    ];

    protected $fillable = [
        'type',
        'model',
        'host',
        'created_at',
    ];

    // public function getCreatedByAttribute($value)
    // {
    //     // if ($value) { //ทำไม ตอน save ถึงทำงาน
    //     return optional(User::selectRaw("concat(firstname, ' ', lastname) as fullname")->find($value))->fullname ?? null;
    //     // }
    // }
}
