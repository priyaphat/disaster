<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function create(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($shelterId && $user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }

    public function update(User $user, News $news): bool
    {
        $shelterId = $news->shelter_id;
        if ($shelterId && $user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }

    public function delete(User $user, News $news): bool
    {
        $shelterId = $news->shelter_id;
        if ($shelterId && $user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }
}
