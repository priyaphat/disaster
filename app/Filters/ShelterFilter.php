<?php

namespace App\Filters;

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class ShelterFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['name'], 'LIKE', '%' . $keyword . '%');
    }

    public function status($value)
    {
        if ($value) {
            return $this->builder->where('status', $value);
        }
    }

    public function max_capacity($value)
    {
        if ($value === 'false') {
            return $this->builder->withCount('populations')
                ->havingRaw('populations_count < max_capacity');
        }
    }

    public function sort($value)
    {
        return $value === 'name' ? $this->builder->orderBy('name', 'desc')
            : $this->builder->orderBy('created_at', $value === 'asc' ? 'asc' : 'desc');
    }

    public function district_code($value)
    {
        if ($value) {
            return $this->builder->where('district_code', $value);
        }
    }

    public function role()
    {
        $role = PermissionUtility::getRoleName(Auth::guard('api')->user());
        if ($role === Role::TEAM_LEADER || $role === Role::TEAM_MEMBER) {
            $allowedShelter = PermissionUtility::getShelterByTeam(Auth::guard('api')->user()->member?->team_id);
            return $this->builder->whereIn('id', $allowedShelter);
        }
    }
}
