<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'notification',
        'entity_id',
        'entity_type',
        'show_details'
    ];

    protected $casts = [
        'notification' => 'array', // หรือใช้ 'json' ก็ได้
    ];
    //     /**
    //  * Interact with the user's first name.
    //  */
    // protected function notification(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (string $value) => json_decode($value),
    //         set: fn (string $value) => json_encode($value),
    //     );
    // }
}
