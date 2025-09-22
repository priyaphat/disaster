<?php

namespace App\Policies;

use App\Http\Utility\PermissionUtility;
use App\Models\AssessmentLogs;
use App\Models\Population;
use App\Models\Role;
use App\Models\User;

class AssessmentLogsPolicy
{
    public function view(User $user, AssessmentLogs $log): bool
    {
        $shelterId = $log->population?->shelterPopulation?->shelter_id;
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function viewAny(User $user, Population $population): bool
    {
        $shelterId = $population->shelterPopulation?->shelter_id;
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }
}
