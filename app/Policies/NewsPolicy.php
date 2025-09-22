<?php

namespace App\Policies;

use App\Models\News;
use App\Models\Role;
use App\Models\User;

class NewsPolicy
{
    public function create(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($shelterId && $user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, News $news): bool
    {
        $shelterId = $news->shelter_id;
        if ($shelterId && $user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user, News $news): bool
    {
        $shelterId = $news->shelter_id;
        if ($shelterId && $user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole(Role::ADMIN);
    }
}
