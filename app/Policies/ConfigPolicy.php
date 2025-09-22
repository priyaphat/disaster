<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class ConfigPolicy
{
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

    public function view(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}
