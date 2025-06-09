<?php

namespace App\Filters;

class UserFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->where(function ($query) use ($keyword) {
            $query->where('firstname', 'like', "%{$keyword}%")
                ->orWhere('lastname', 'like', "%{$keyword}%")
                ->orWhereRaw("CONCAT(firstname, ' ', lastname) LIKE ?", ["%{$keyword}%"]);
        });
    }

    // public function team($id)
    // {
    //     return $this->builder->whereHas('team', function ($query) use ($id) {
    //         $query->where('id', $id);
    //     });
    // }
    public function pending($value)
    {
        return $this->builder->whereHas('approval', function ($query) use ($value) {
            $query->where('is_approved', $value);
        });
    }

    public function approve($array)
    {
        return $this->builder->where(function ($query) use ($array) {
            $query->where('registration_type', $array['type'])
            ->orWhereHas('approval', function ($query) use ($array) {
            $query->where('is_approved', $array['status']);
        });
        });
    }

    public function type($value)
    {
        $value = intval($value);
        return $this->builder->where('type', $value);
    }

    public function province($id)
    {
        return $this->builder->whereHas('area', function ($query) use ($id) {
            $query->where('province_code', $id);
        });
    }

    public function district($id)
    {
        return $this->builder->where('district_id', $id);
    }

    public function position($id)
    {
        return $this->builder->where('position_id', $id);
    }

    public function department($id)
    {
        return $this->builder->where('department_id', $id);
    }
}
