<?php

namespace App\Policies;

use App\Models\ShelterStaff;
use App\Models\User;

class ShelterStaffPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function createMany(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }


    public function update(User $user, ShelterStaff $shelterStaff): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterStaff->shelter_id;
        }

        return $user->hasRole('admin');
    }

    public function delete(User $user, ShelterStaff $shelterStaff): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterStaff->shelter_id;
        }

        return $user->hasRole('admin');
    }

    public function deleteAll(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }
}
