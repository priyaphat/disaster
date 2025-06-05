<?php

namespace App\Models;

use App\Filters\AppointmentFilter;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AppointmentShift extends Model
{
    //
    public $timestamps = false;
    protected $fillable = [
        'appointment_id',
        'shift',
        'shift_date',
        'shift_start_time',
        'shift_end_time',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'appointment_id', 'id');
    }

    protected function ShiftDate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Carbon::parse($value)->format('d-m-Y') : null,
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null
        );
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new AppointmentFilter($filters))->apply($query);
    }
}
