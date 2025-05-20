<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\CreatedUpdatedBy;

class Disaster extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'name',
    ];


}
