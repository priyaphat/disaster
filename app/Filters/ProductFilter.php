<?php

namespace App\Filters;

use Carbon\Carbon;

class ProductFilter extends QueryFilter
{
    public function search($keyword)
    {
        if ($keyword) return $this->builder->whereAny(['products.name'], 'LIKE', '%' . $keyword . '%');
    }

    public function unit_id($value)
    {
        if ($value) {
            if ($value == -1) return $this->builder->WhereNull('products.unit_id');
            else return $this->builder->where('products.unit_id', $value);
        }
    }

    public function category_id($value)
    {
        if ($value)  return $this->builder->where('products.category_id', $value);
    }

    public function sort($value)
    {
        if ($value) return $this->builder->orderBy('products.created_at', $value === 'asc' ? 'asc' : 'desc');
    }
}
