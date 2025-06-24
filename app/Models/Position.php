<?php

namespace App\Models;

use App\Filters\PositionFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use CreatedUpdatedBy;
     
    protected $fillable = [
        'name',
    ];

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new PositionFilter($filters))->apply($query);
    }
}
