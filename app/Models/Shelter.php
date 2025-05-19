<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class Shelter extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'name',
        'phone',
        'created_date',
        'status',
        'leader_id',
        'address',
        'max_capacity',
    ];
}
