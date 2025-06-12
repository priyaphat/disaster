<?php

namespace App\Filters;

class TeamFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['name'], 'LIKE', '%' . $keyword . '%');
    }
}
