<?php

namespace App\Policies;

use App\Models\Shelter;
use App\Models\User;

class ShelterPolicy
{
    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Shelter $shelter): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelter->id;
        }
        return $this->isAdmin($user);
    }
}
