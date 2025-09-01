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

    public function view(User $user, Shelter $shelter): bool
    {
        if ($shelter->status == Shelter::OPEN) {
            return true;
        }
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function viewAny(User $user): bool
    {
        return true;
        // return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
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
