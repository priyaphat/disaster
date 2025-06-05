<?php

namespace App\Models;

use App\Filters\AppointmentFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    //
    use CreatedUpdatedBy;

    protected $fillable = [
        'start_time',
        'end_time',
        'shelter_id',
        'team_id'
    ];

    public function team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }

    public function shelter(): HasOne
    {
        return $this->hasOne(Shelter::class, 'id', 'shelter_id');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(AppointmentShift::class, 'appointment_id', 'id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new AppointmentFilter($filters))->apply($query);
    }
}
