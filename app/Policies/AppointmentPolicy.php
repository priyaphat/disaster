<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\User;

class AppointmentPolicy
{
    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        $shelterId = $appointment->shelter_id;
        $teamId = $appointment->teamId;
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        } else if ($user->hasAnyRole([Role::TEAM_LEADER, Role::TEAM_MEMBER])) {
            return $user->member?->team_id == $teamId;
        }

        return $user->hasRole(Role::ADMIN);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::STAFF, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    /**
     * Determine whether the user can view the notification.
     */
    public function viewNoti(User $user, Appointment $appointment): bool
    {
        $shelterId = $appointment->shelter_id;
        $teamId = $appointment->teamId;
        if ($user->hasRole(Role::STAFF)) {
            return $user->staff?->shelter_id == $shelterId;
        } else if ($user->hasAnyRole([Role::TEAM_LEADER, Role::TEAM_MEMBER])) {
            return $user->member?->team_id == $teamId;
        }

        return $user->hasRole(Role::ADMIN);
    }
}
