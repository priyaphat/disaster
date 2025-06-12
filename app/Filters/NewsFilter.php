<?php

namespace App\Filters;

use App\Http\Utility\ConvertUtility;
use App\Http\Utility\DateUtillity;
use Illuminate\Support\Facades\Auth;

class NewsFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->whereAny(['title'], 'LIKE', '%' . $keyword . '%');
    }

    public function limit($value)
    {
        $value = ConvertUtility::toInt($value);
        if ($value) {
            return $this->builder->take($value);
        }
    }

    public function range($value)
    {
        if ($value) {
            $rangeDate = explode('-', $value);
            $startDate = DateUtillity::createDMYtoAD($rangeDate[0])->startOfMonth();
            $endDate = DateUtillity::createDMYtoAD($rangeDate[1])->endOfMonth();
            return $this->builder->whereBetween('created_at', [$startDate, $endDate]);
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
