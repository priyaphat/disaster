<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class ShelterTypePolicy
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
}
