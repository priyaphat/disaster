<?php

namespace App\Filters;

use Carbon\Carbon;

class ProductFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['name'], 'LIKE', '%' . $keyword . '%');
    }

    public function unit_id($value)
    {
        return $this->builder->where('unit_id', $value);
    }

    public function category_id($value)
    {
        return $this->builder->where('category_id', $value);
    }

    public function sort($value)
    {
        return $this->builder->orderBy('created_at', $value === 'asc' ? 'asc' : 'desc');
    }
}
