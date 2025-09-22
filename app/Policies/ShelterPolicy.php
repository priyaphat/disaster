<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Shelter;
use App\Models\User;

class ShelterPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, Shelter $shelter): bool
    {
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelter->id;
        }
        return $user->hasRole(Role::ADMIN);
    }
}
