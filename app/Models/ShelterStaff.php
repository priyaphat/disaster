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

    public function prefix()
    {
        return $this->belongsTo(Prefix::class, 'prefix_id')->select('id', 'name');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id')->select('id', 'name');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->select('id', 'name');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'prefix_id', 'firstname', 'lastname', 'position_id', 'department_id');
    }
}
