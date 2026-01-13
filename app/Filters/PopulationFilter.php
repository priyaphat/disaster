<?php

namespace App\Filters;

use App\Http\Utility\ConvertUtility;
use App\Models\Population;
use Carbon\Carbon;

class PopulationFilter extends QueryFilter
{
    public function search($keyword)
    {
        return $this->builder->where(function ($query) use ($keyword) {
            $query->where('first_name', 'like', "%{$keyword}%")
                ->orWhere('last_name', 'like', "%{$keyword}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"]);
        });
    }

    public function gender($value)
    {
        if ($value) {
            return $this->builder->where('gender', $value);
        }
    }

    public function status($value)
    {
        if ($value) {
            return $this->builder->where('status', $value);
        }
    }

    public function province_id($value)
    {
        if ($value) {
            return $this->builder->whereHas('currentAddress', function ($query) use ($value) {
                $query->where('province_code', $value);
            });
        }
    }

    public function district_id($value)
    {
        if ($value) {
            return $this->builder->whereHas('currentAddress', function ($query) use ($value) {
                $query->where('district_code', $value);
            });
        }
    }

    public function district($value)
    {
        if ($value) {
            return $this->builder->whereHas('currentAddress', function ($query) use ($value) {
                $query->where('district_code', $value);
            });
        }
    }

    public function shelter($value)
    {
        if ($value === 'true') {
            return $this->builder->whereHas('shelterPopulation', function ($query) use ($value) {
                $query->whereNotNull('shelter_id');
            });
        }

        if ($value === 'false') {
            return $this->builder->whereHas('shelterPopulation', function ($query) use ($value) {
                $query->whereNull('shelter_id');
            });
        }
    }

    public function shelter_id($value)
    {
        if ($value) {
            return $this->builder->whereHas('shelterPopulation', function ($query) use ($value) {
                if (is_array($value)) {
                    $query->whereIn('shelter_id', $value);
                } else {
                    $query->where('shelter_id', $value);
                }
            });
        }
    }

    public function vulnerable_group_id($id)
    {
        if (strlen($id) > 0) {
            $id = $id == 0 ? null : $id; // ID 0 = ไม่มีกลุ่มเปราะบาง
            return $this->builder->where('vulnerable_group_id', $id);
        }
    }

    public function tracking()
    {
        return $this->builder->where('tracking', Population::tracking);
    }

    public function sort($value)
    {
        $sort = $value ?? 'desc';
        return $this->builder->orderBy('created_at', $sort === 'asc' ? 'asc' : 'desc');
    }
}
