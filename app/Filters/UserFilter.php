<?php

namespace App\Filters;

use App\Enums\ApprovalStatus;
use App\Enums\RegistrationType;
use App\Enums\UserStatus;
use App\Http\Utility\ConvertUtility;
use App\Models\Role;
use Illuminate\Support\Str;

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

    public function approve($value)
    {
        if ($value !== 'pending') {
            return $this->builder->where(function ($query) {
                $query->where('registration_type', RegistrationType::ADMIN->value)
                    ->orWhereHas('approval', function ($query) {
                        $query->where('is_approved', ApprovalStatus::APPROVED->value);
                    });
            });
        } else {
            return $this->builder->whereHas('approval', function ($query) {
                $query->where('is_approved', ApprovalStatus::PENDING->value);
            });
        }
    }

    public function type($value)
    {
        $value = ConvertUtility::toInt($value);
        if ($value) {
            return $this->builder->where('type', $value);
        }
    }

    public function province_id($id)
    {
        $id = ConvertUtility::toInt($id);
        return $this->builder->whereHas('area', function ($query) use ($id) {
            if ($id) {
                $query->where('province_code', $id);
            }
        });
    }

    public function district_id($id)
    {
        $id = ConvertUtility::toInt($id);
        if ($id) {
            return $this->builder->where('district_id', $id);
        }
    }

    public function position_id($id)
    {
        $id = ConvertUtility::toInt($id);
        if ($id) {
            return $this->builder->where('position_id', $id);
        }
    }

    public function department_id($id)
    {
        $id = ConvertUtility::toInt($id);
        if ($id) {
            return $this->builder->where('department_id', $id);
        }
    }

    public function status($value)
    {
        $status = UserStatus::fromName($value)->value;
        return $this->builder->where('status', $status);
    }

    public function available()
    {
        // Doesn't show admin role.
        return $this->builder->whereDoesntHave('roles')->whereDoesntHave('roles', function ($query) {
            $query->where('name', Role::ADMIN);
        });
    }

    // public function available_staff()
    // {
    //     return $this->builder->doesntHave('staff');
    // }

    // public function available_member()
    // {
    //     return $this->builder->doesntHave('member');
    // }
}
