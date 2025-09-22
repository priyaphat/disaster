<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class AppointmenLogPolicy
{
    public function update(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}
