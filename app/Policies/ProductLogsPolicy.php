<?php

namespace App\Policies;

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\User;

class ProductLogsPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::STAFF);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(Role::STAFF);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(Role::STAFF);
    }
}
