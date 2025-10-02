<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class DamageUtilityPolicy
{
    public function view(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    /**
     * Determine whether the user can view the notification.
     */
    public function viewNoti(User $user): bool
    {
        return true;
    }
}
