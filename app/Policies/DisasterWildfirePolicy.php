<?php

namespace App\Policies;

use App\Models\DisasterWildfire;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DisasterWildfirePolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}
