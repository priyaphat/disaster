<?php

namespace App\Filters;

use Carbon\Carbon;

class TransferHistoryFilter extends QueryFilter
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

    public function date($value)
    {
        $value = explode(',', $value);
        $start = Carbon::parse($value[0])->format('Y-m-d') ?? null;
        $end = Carbon::parse($value[1])->format('Y-m-d') ?? null;
        return $this->builder->whereBetween('created_at', [$start, $end]);
    }
}
