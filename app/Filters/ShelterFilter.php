<?php

namespace App\Filters;

use App\Models\Population;
use Carbon\Carbon;

class ShelterFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['name'], 'LIKE', '%' . $keyword . '%');
    }

    public function status($value)
    {
        if($value){
            return $this->builder->where('status', $value);
        }
    }

    // public function action_type($value)
    // {
    //     return $this->builder->where('action_type', $value);
    // }

    // public function year($value)
    // {
    //     return $this->builder->whereYear('created_at', $value);
    // }

    public function sort($value)
    {
        return $value === 'name' ? $this->builder->orderBy('name', 'desc')
            : $this->builder->orderBy('created_at', $value === 'asc' ? 'asc' : 'desc');
    }

    public function district_code($value)
    {
        if($value){
            return $this->builder->where('district_code', $value);
        }
    }
}
