<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class UserPolicy
{
    public function view(User $user): bool
    {
       return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER]);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER]);
    }

    public function viewList(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, User $userUpdated): bool
    {
        return $user->hasRole(Role::ADMIN) || $user->id == $userUpdated->id;
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function approve(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function transfer(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function assignRole(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function deleteRole(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }


    /**
     * Handle the Shelter Population "updated" event.
     */
    public function viewNoti(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}
