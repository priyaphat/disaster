<?php

namespace App\Policies;

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\ShelterProduct;
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

    /**
     * Handle the Shelter Population "updated" event.
     */
    public function viewNoti(User $user, ShelterProduct $product): bool
    {
        $shelterId = $product->shelter_id;
        if ($shelterId && $user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }
        return $user->hasRole(Role::ADMIN);
    }
}
