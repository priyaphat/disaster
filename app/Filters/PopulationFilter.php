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
        return $this->builder->where('gender', $value);
    }

    public function status($value)
    {
        return $this->builder->where('status', $value);
    }

    public function shelter()
    {
        return $this->builder->whereNotNull('shelter_id');
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
