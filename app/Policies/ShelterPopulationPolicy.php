<?php

namespace App\Policies;

use App\Models\Population;
use App\Models\User;

class ShelterPopulationPolicy
{
    public function create(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }

    public function update(User $user, Population $population): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $population->shelterPopulation?->shelter_id;
        }
        return $user->hasRole('admin');
    }

    public function updateAll(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }
}
