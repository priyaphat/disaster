<?php

namespace App\Policies;

use App\Http\Utility\PermissionUtility;
use App\Models\Assessments;
use App\Models\Population;
use App\Models\Role;
use App\Models\User;

class AssessmentsPolicy
{
    public function view(User $user, Assessments $assessments): bool
    {
        $shelterId = $assessments->population?->shelterPopulation?->shelter_id;
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

    public function create(User $user, Population $population): bool
    {
        $shelterId = $population->shelterPopulation?->shelter_id;
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        } 

        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function update(User $user, Assessments $assessments): bool
    {
        $shelterId = $assessments->population?->shelterPopulation?->shelter_id;
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        } 
        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function delete(User $user, Assessments $assessments): bool
    {
        $shelterId = $assessments->population?->shelterPopulation?->shelter_id;
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        } 

        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }
}
