<?php

namespace App\Filters;

use App\Http\Utility\ConvertUtility;
use App\Http\Utility\DateUtillity;
use Carbon\Carbon;

class AppointmentFilter extends QueryFilter
{
    public function team_id($id)
    {
        $id = ConvertUtility::toInt($id);
        if ($id) {
            return $this->builder->where('team_id', $id);
        }
    }

    public function shelter_id($id)
    {
        $id = ConvertUtility::toInt($id);
        if ($id) {
            return $this->builder->where('shelter_id', $id);
        }
    }

    public function selected_month($month)
    {
        $startDate = $month ? DateUtillity::createAD($month)->startOfMonth() : Carbon::now()->startOfMonth();
        $endDate = $month ? DateUtillity::createAD($month)->endOfMonth() : Carbon::now()->endOfMonth();
        return $this->builder->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('appointment_date', [$startDate, $endDate]);
        });
    }
}
