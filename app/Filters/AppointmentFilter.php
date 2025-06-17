<?php

namespace App\Filters;

use App\Http\Utility\ConvertUtility;
use Carbon\Carbon;

class AppointmentFilter extends QueryFilter
{
    public function team_id($id)
    {
        if ($id) {
            $id = ConvertUtility::toInt($id);
            return $this->builder->where('team_id', $id);
        }
    }

    public function shelter_id($id)
    {
        if ($id) {
            $id = ConvertUtility::toInt($id);
            return $this->builder->where('shelter_id', $id);
        }
    }

    public function selected_month($month)
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
        return $this->builder->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('appointment_date', [$startDate, $endDate]);
        });
    }
}
