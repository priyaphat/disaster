<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class ShelterPosition extends Model
{
    use CreatedUpdatedBy;
     
    protected $fillable = [
        'name',
    ];
}
