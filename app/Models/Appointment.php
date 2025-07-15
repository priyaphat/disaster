<?php

namespace App\Models;

use App\Filters\AppointmentFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    //
    use CreatedUpdatedBy;

    protected $appends = [
        'info_disabled'
    ];

    protected $fillable = [
        'appointment_date',
        'start_time',
        'end_time',
        'shift',
        'shelter_id',
        'team_id'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date:Y-m-d',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }

    public function shelter(): HasOne
    {
        return $this->hasOne(Shelter::class, 'id', 'shelter_id');
    }

    public function teamLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parentable_id', 'team_id')
            ->where('parentable_type', Team::class)
            ->join('appointments', 'appointment_logs.parentable_id', '=', 'appointments.team_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.appointment_date)');
    }

    public function userLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parentable_id', 'team_id')
            ->where('parentable_type', User::class)
            ->join('appointments', 'appointment_logs.parentable_id', '=', 'appointments.team_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.appointment_date)');
    }

    public function shelterLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parentable_id', 'shelter_id')
            ->where('parentable_type', Shelter::class)
            ->join('appointments', 'appointment_logs.parentable_id', '=', 'appointments.shelter_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.appointment_date)');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new AppointmentFilter($filters))->apply($query);
    }

    /**
     * Get the disabled appointment.
     */
    protected function getInfoDisabledAttribute()
    {
        if ($this->appointment_date) {
            $date = Carbon::parse($this->appointment_date)->format('Y-m-d');
            $datetime = Carbon::parse("{$date} {$this->end_time}");
            if (Carbon::now()->greaterThan($datetime)) {
                return true;
            }
            return false;
        }
        return false;
    }
}
