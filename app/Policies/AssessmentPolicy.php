<?php

namespace App\Policies;

use App\Models\Assessments;
use App\Models\User;

class AssessmentPolicy
{
    public function view(User $user, Assessments $assessments): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $assessments->population->shelterPopulation?->shelter_id;
        }

        return $user->hasRole('admin');
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function create(User $user): bool
    {
        $shelterId = request()->get('shelter_id');
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasRole('admin');
    }

    public function update(User $user, Assessments $assessments): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $assessments->population->shelterPopulation?->shelter_id;
        }
        return $user->hasRole('admin');
    }

    public function delete(User $user, Assessments $assessments): bool
    {
        if ($user->hasRole('staff')) {
            return $user->staff?->shelter_id == $assessments->population->shelterPopulation?->shelter_id;
        }

        return $user->hasRole('admin');
    }
}
