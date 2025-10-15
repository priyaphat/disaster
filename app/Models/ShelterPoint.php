<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class ShelterPoint extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'external_id',
        'original_name',
        'normalized_name',
        'map_url',
        'latitude',
        'longitude'
    ];
}
