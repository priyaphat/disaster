<?php

namespace App\Policies;

use App\Models\Population;
use App\Models\User;

class PopulationPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

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

    public function delete(User $user, Population $population): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $population->shelterPopulation?->shelter_id;
        }

        return $user->hasRole('admin');
    }
}
