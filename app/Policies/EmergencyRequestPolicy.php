<?php

namespace App\Policies;

use App\Models\User;

class EmergencyRequestPolicy
{
    public function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'team_leader', 'team_memebr']);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'team_leader', 'team_memebr']);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user): bool
    {
        return $this->isAdmin($user) || $user->hasRole('team_leader');
    }

    public function delete(User $user): bool
    {
        return $this->isAdmin($user);
    }
}
