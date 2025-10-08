<?php

namespace App\Models;

use App\Enums\AllergyStatus;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PopulationAllergy extends Model
{
    //
    use CreatedUpdatedBy, LogsActivity;

    protected $table = 'population_allergies';

    protected $fillable = [
        'population_id',
        'status',
        'drug',
        'food',
        'other',
    ];

    protected $casts = [
        'population_id' => 'integer',
        'status' => AllergyStatus::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('patient_allergies')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }
}
