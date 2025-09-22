<?php

namespace App\Policies;

use App\Models\DisasterSmog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DisasterSmogPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user): bool
    {
        return true;
    }
}
