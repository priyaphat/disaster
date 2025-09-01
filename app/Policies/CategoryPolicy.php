<?php

namespace App\Policies;

use App\Models\User;

class CategoryPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff']);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
