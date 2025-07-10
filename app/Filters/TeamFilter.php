<?php

namespace App\Filters;

class TeamFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['name'], 'LIKE', '%' . $keyword . '%')
            ->orWhereHas('leader', function ($query) use ($keyword) {
                $query->whereAny(['firstname', 'lastname'], 'LIKE', '%' . $keyword . '%')
                    ->orWhereRaw("concat(firstname, ' ', lastname) like '%$keyword%' ");
            });
    }
}
