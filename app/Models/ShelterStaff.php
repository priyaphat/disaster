<?php

namespace App\Models;

use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ShelterStaff extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    protected $table = 'shelter_staffs';

    public const exit = null;

    protected $fillable = [
        'user_id',
        'shelter_id',
        'shelter_position_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('shelter_staffs')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function logs()
    {
        return $this->morphMany(TransferHistory::class, 'parentable');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id')->select('id', 'name');
    }

    public function prefix()
    {
        return $this->belongsTo(Prefix::class, 'prefix_id')->select('id', 'name');
    }

    public function shelter()
    {
        return $this->hasOne(Shelter::class, 'id', 'shelter_id')->select('id', 'name');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->select('id', 'name');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shelterPosition()
    {
        return $this->belongsTo(ShelterPosition::class, 'shelter_position_id')->select('id', 'name');
    }
}
