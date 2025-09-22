<?php

namespace App\Policies;

use App\Models\EmergencyRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EmergencyRequestPolicy
{
    public function view(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function update(User $user, EmergencyRequest $emergencyRequest): bool
    {
        if ($emergencyRequest['status'] === EmergencyRequest::waiting) {
            return $user->hasRole(Role::ADMIN);
        } else if ($emergencyRequest['status'] === EmergencyRequest::approve) {
            return $user->hasRole(Role::ADMIN) || ($user->hasRole(Role::TEAM_LEADER) && $emergencyRequest->emergency_team_id === $user->member?->team_id); // เช็กด้วยว่าทีมตรงกันไหม

        } else if ($emergencyRequest['status'] === EmergencyRequest::helping) {
            return $user->hasAnyRole([Role::TEAM_LEADER, Role::TEAM_MEMBER]) && $emergencyRequest->emergency_team_id === $user->member?->team_id; // เช้็กด้วยว่าทีมตรงกันไหม
        }
        return false;
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function requestShelter(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }
}
