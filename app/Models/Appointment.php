<?php

namespace App\Models;

use App\Filters\AppointmentFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
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

    public function teamLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parent_id', 'team_id')
            ->where('parent_type', \App\Models\Team::class)
            ->join('appointments', 'appointment_logs.parent_id', '=', 'appointments.team_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.start_time)');
    }

    public function userLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parent_id', 'team_id')
            ->where('parent_type', \App\Models\User::class)
            ->join('appointments', 'appointment_logs.parent_id', '=', 'appointments.team_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.start_time)');
    }

    public function shelterLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parent_id', 'shelter_id')
            ->where('parent_type', \App\Models\Shelter::class)
            ->join('appointments', 'appointment_logs.parent_id', '=', 'appointments.shelter_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.start_time)');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new AppointmentFilter($filters))->apply($query);
    }
}
