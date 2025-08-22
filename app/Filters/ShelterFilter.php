<?php

namespace App\Filters;

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
}
