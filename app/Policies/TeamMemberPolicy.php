<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\TeamMember;
use App\Models\User;

class TeamMemberPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function create(User $user): bool
    {
        $teamId = request()->get('team_id');
        if($user->hasRole(Role::TEAM_LEADER)){
            return $user->member?->team_id === $teamId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, TeamMember $teamMember): bool
    {
        $teamId = $teamMember->team_id;
        if($user->hasRole(Role::TEAM_LEADER)){
            return $user->member?->team_id === $teamId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user, TeamMember $teamMember): bool
    {
        $teamId = $teamMember->team_id;
        if($user->hasRole(Role::TEAM_LEADER)){
            return $user->member?->team_id === $teamId;
        }

        return $user->hasRole(Role::ADMIN);
    }
}
