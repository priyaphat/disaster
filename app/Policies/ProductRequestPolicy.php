<?php

namespace App\Policies;

use App\Http\Utility\PermissionUtility;
use App\Http\Utility\ProductUtility;
use App\Models\ProductRequest;
use App\Models\Role;
use App\Models\User;

class ProductRequestPolicy
{
    public function viewAny(User $user): bool
    {
        $shelterId = request()->get('shelter_id') ?? ProductUtility::getShelter();
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        if ($user->hasAnyRole([Role::TEAM_LEADER, Role::TEAM_MEMBER])) {
            $allowedShelter = PermissionUtility::getShelterByTeam($user->member?->team_id);
            return $allowedShelter->search($shelterId);
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function viewAnyLog(User $user): bool
    {
        return $user->hasRole(Role::STAFF);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::STAFF);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(Role::STAFF);
    }

    /**
     * Determine whether the user can view the notification.
     */
    public function viewNoti(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}
