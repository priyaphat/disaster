<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    private function hasAllowedRole(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user): bool
    {
        return $this->hasAllowedRole($user);
    }

    public function viewAny(User $user): bool
    {
        return $this->hasAllowedRole($user);
    }
}
