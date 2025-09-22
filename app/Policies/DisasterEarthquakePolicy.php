<?php

namespace App\Policies;

use App\Models\DisasterEarthquake;
use App\Models\Role;
use App\Models\User;

class DisasterEarthquakePolicy
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
