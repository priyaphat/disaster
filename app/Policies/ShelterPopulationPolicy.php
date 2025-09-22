<?php

namespace App\Policies;

use App\Models\Population;
use App\Models\Role;
use App\Models\User;

class ShelterPopulationPolicy
{
    public function create(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, Population $population): bool
    {
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $population->shelterPopulation?->shelter_id;
        }
        return $user->hasRole(Role::ADMIN);
    }

    public function updateAll(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole(Role::ADMIN);
    }
}
