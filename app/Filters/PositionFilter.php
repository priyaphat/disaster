<?php

namespace App\Filters;

class PositionFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['name'], 'LIKE', '%' . $keyword . '%');
    }
}
