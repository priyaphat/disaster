<?php

namespace App\Filters;

use App\Http\Utility\ConvertUtility;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NewsFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['title'], 'LIKE', '%' . $keyword . '%');
    }

    public function limit($value)
    {
        if ($value) {
            $value = ConvertUtility::toInt($value);
            return $this->builder->take($value);
        }
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

    public function shelter_id($id)
    {
        $id = ConvertUtility::toInt($id);
        return $this->builder->where('shelter_id', $id);
    }

    // ข่าวจากศูนย์กลาง
    public function center()
    {
        return $this->builder->whereNull('shelter_id');
    }

    public function type($value)
    {
        if ($value === 'self') {
            return $this->builder->where('created_by', Auth::user()->id);
        }
    }

    public function sort($value)
    {
        if ($value) {
            return $this->builder->orderBy('updated_at', $value);
        }
    }
}
