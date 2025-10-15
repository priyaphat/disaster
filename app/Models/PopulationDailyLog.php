<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;

class PopulationDailyLog extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'log_date',
        'population_id',
        'shelter_id',
        'status',
        'death_cause',
        'description',
    ];
}
