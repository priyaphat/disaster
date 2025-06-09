<?php

namespace App\Filters;

class AppointmentFilter extends QueryFilter
{
    public function shelter($id)
    {
        return $this->builder->with('appointment.shelter', function ($query) use ($id) {
            if($id){
                $query->where('id', $id);
            }
        })->whereHas('appointment.shelter', function ($query) use ($id) {
            if($id){
                $query->where('id', $id);
            }
        });
    }

    public function team($id)
    {
        return $this->builder->with('appointment.team', function ($query) use ($id) {
            if($id){
                $query->where('id', $id);
            }
        })->whereHas('appointment.team', function ($query) use ($id) {
            if($id){
                $query->where('id', $id);
            }
        });
    }
}
