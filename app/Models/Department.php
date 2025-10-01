<?php

namespace App\Models;

use App\Filters\DepartmentFilter;
use App\Http\Traits\CreatedUpdatedBy;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use CreatedUpdatedBy;

    protected $fillable = [
        'name'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'id', 'department_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return (new DepartmentFilter($filters))->apply($query);
    }
}
