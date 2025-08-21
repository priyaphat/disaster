<?php

namespace App\Filters;

use App\Models\Population;
use Carbon\Carbon;

class PopulationFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->where(function ($query) use ($keyword) {
            $query->where('first_name', 'like', "%{$keyword}%")
                ->orWhere('last_name', 'like', "%{$keyword}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
        });
    }

    public function gender($value)
    {
        if ($value) {
            return $this->builder->where('gender', $value);
        }
    }

    public function status($value)
    {
        if ($value) {
            return $this->builder->where('status', $value);
        }
    }

    public function district($value)
    {
        if ($value) {
            return $this->builder->whereHas('currentAddress', function ($query) use ($value) {
                $query->where('district_code', $value);
            });
        }
    }

    public function shelter($value)
    {
        if ($value === 'true') {
            return $this->builder->whereHas('shelterPopulation');
        }

        if ($value === 'false') {
            return $this->builder->whereDoesntHave('shelterPopulation');
        }
    }

    public function shelter_id($value)
    {
        if ($value) {
            return $this->builder->whereHas('shelterPopulation', function ($query) use ($value) {
                $query->where('shelter_id', $value);
            });
        }
    }

    public function tracking()
    {
        return $this->builder->where('tracking', Population::tracking);
    }

    public function sort($value)
    {
        $sort = $value ?? 'desc';
        return $this->builder->orderBy('created_at', $sort === 'asc' ? 'asc' : 'desc');
    }
}
