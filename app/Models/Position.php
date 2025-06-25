<?php

namespace App\Models;

use App\Filters\PositionFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    use CreatedUpdatedBy;
     
    protected $fillable = [
        'name',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'position_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new PositionFilter($filters))->apply($query);
    }
}
