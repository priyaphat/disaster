<?php

namespace App\Policies;

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\User;

class ShelterProductPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function viewAny(User $user): bool
    {
        $shelterId = request()->get('id');
        if ($user->hasAnyRole([Role::TEAM_LEADER, Role::TEAM_MEMBER])) {
            $allowedShelter = PermissionUtility::getShelterByTeam($user->member?->team_id);
            return $allowedShelter->search($shelterId);
        }

        return $user->hasAnyRole([Role::ADMIN, Role::STAFF]);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF]);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF]);
    }

    public function delete(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF]);
    }
}
