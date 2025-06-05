<?php

namespace App\Filters;

class AppointmentFilter extends QueryFilter
{
    public function shelter($id)
    {
        return $this->builder->where('shelter_id', $id);
    }

    public function team($id)
    {
        return $this->builder->where('team_id', $id);
    }
}
