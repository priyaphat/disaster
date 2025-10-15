<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class ShelterDailyLog extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'log_date',
        'shelter_id',
        'status',
        'max_capacity',
        'current_capacity',
        'total_populations',
        'total_injuries',
        'total_healthy',
        'total_deaths',
        'description',
    ];
}
