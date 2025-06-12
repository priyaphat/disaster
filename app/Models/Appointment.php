<?php

namespace App\Models;

use App\Casts\AppointmentDateFormat;
use App\Casts\DateFormat;
use App\Casts\DateSlashFormat;
use App\Casts\DateTimeFormat;
use App\Casts\ThaiDateFormat;
use App\Casts\ThaiDateTimeDisplayFormat;
use App\Casts\ThaiDateTimeFormat;
use App\Casts\ThaiSlashDateFormat;
use App\Enums\ShiftType;
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
            'appointment_date' => AppointmentDateFormat::class,
            'created_at' => ThaiDateTimeFormat::class,
            'updated_at' => ThaiDateTimeFormat::class,
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
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.start_time)');
    }

    public function userLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parentable_id', 'team_id')
            ->where('parentable_type', User::class)
            ->join('appointments', 'appointment_logs.parentable_id', '=', 'appointments.team_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.start_time)');
    }

    public function shelterLog()
    {
        return $this->hasOne(AppointmentLog::class, 'parentable_id', 'shelter_id')
            ->where('parentable_type', Shelter::class)
            ->join('appointments', 'appointment_logs.parentable_id', '=', 'appointments.shelter_id')
            ->whereRaw('DATE(appointment_logs.log_date) = DATE(appointments.start_time)');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new AppointmentFilter($filters))->apply($query);
    }
}
