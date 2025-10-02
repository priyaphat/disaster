<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class ProductPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF]);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

}
