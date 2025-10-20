<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class ShelterDailyTotal extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'log_date',
        'total_shelter',
        'total_victims',
        'total_injuries',
        'total_healthy',
        'total_deaths',
        'description',
    ];
}
