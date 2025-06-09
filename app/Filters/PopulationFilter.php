<?php

namespace App\Filters;

class PopulationFilter extends QueryFilter
{
    public function action_type($value)
    {
        return $this->builder->where('action_type', $value);
    }

    public function year($value)
    {
        return $this->builder->whereYear('created_at', $value);
    }

    public function sort($value)
    {
        return $this->builder->orderBy('created_at', $value === 'asc' ? 'asc' : 'desc');
    }
}
