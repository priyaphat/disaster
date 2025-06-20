<?php

namespace App\Filters;

use Carbon\Carbon;

class ProductRequestFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereHas('product', function ($query) use ($keyword) {
            $query->whereAny(['name'], 'LIKE', '%' . $keyword . '%');
        });
    }

    public function shelter_id($value)
    {
        return $this->builder->where('shelter_id', $value);
    }

    public function unit_id($value)
    {
        return $this->builder->whereHas('product', function ($query) use ($value) {
            $query->where('unit_id', $value);
        });
    }

    public function category_id($value)
    {
        return $this->builder->whereHas('product', function ($query) use ($value) {
            $query->where('category_id', $value);
        });
    }

    public function sort($value)
    {
        return $this->builder->orderBy('created_at', $value === 'asc' ? 'asc' : 'desc');
    }
}
