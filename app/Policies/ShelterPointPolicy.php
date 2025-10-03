<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class ShelterPointPolicy
{
    public function search(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF]);
    }
}
