<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\ShelterStaff;
use App\Models\User;

class ShelterStaffPolicy
{
    public function view(User $user, ShelterStaff $staff): bool
    {
        $shelterId = $staff->shelter_id;
        if ($shelterId && $user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }
        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function createMany(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, ShelterStaff $shelterStaff): bool
    {
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterStaff->shelter_id;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user, ShelterStaff $shelterStaff): bool
    {
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterStaff->shelter_id;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function deleteAll(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    /**
     * Handle the Shelter Population "updated" event.
     */
    public function viewNoti(User $user, ShelterStaff $staff): bool
    {
        $shelterId = $staff->shelter_id;
        if ($shelterId && $user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }
        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }
}
