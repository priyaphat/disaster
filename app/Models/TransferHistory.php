<?php

namespace App\Models;

use App\Filters\TransferHistoryFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TransferHistory extends Model
{
    use CreatedUpdatedBy, LogsActivity;

    public const EXIT = 1;
    public const TRANSFER = 2;
    public const ENTER = 3;
    public const ASSIGN = 4 ;
    public const JOIN_TEAM = 5;
    public const CHANGE_TEAM = 6;
    public const OPEN = 7;
    public const CLOSE = 8;

    protected $fillable = [
        'action_type',
        'parentable_type',
        'parentable_id',
        'childable_type',
        'childable_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('transfer_histories')
            ->logOnly(['*'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    public function parent()
    {
        return $this->morphTo();
    }

    public function created_by_name()
    {
        return $this->belongsTo(User::class, 'created_by')->select(['id', 'firstname', 'lastname']);
    }

    public function shelter()
    {
        return $this->belongsTo(Shelter::class, 'childable_id')->select(['id', 'name']);
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'childable_id')->select(['id', 'name']);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new TransferHistoryFilter($filters))->apply($query);
    }
}
