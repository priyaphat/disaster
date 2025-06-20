<?php

namespace App\Filters;

use Carbon\Carbon;

class TransferHistoryFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereHas('shelter', function ($query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        });
    }

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

    public function district_code($value)
    {
        return $this->builder->whereHas('shelter', function ($query) use ($value) {
            $query->where('district_code', $value);
        });
    }

    public function start_date($value)
    {
        if ($value) {
            $startDate = Carbon::parse($value)->format('Y-m-d');
            return $this->builder->whereDate('created_at', '>=', $startDate);
        }
    }

    public function end_date($value)
    {
        if ($value) {
            $endDate = Carbon::parse($value)->format('Y-m-d');
            return $this->builder->whereDate('created_at', '<=', $endDate);
        }
    }
}
