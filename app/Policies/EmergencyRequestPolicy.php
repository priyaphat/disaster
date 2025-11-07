<?php

namespace App\Policies;

use App\Models\EmergencyRequest;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;

class EmergencyRequestPolicy
{

    public function adminOnly(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function view(User $user, EmergencyRequest $emergencyRequest): bool
    {
        $emergencyRequest->load('emergencyTeams');
        $teamArr = $emergencyRequest->emergencyTeams->pluck('team_id')->toArray();
        if ($user->hasRole(Role::TEAM_LEADER)) {
            return in_array($user->member?->team_id, $teamArr) && in_array($emergencyRequest['status'], [EmergencyRequest::approve, EmergencyRequest::helping, EmergencyRequest::complete, EmergencyRequest::incomplete, EmergencyRequest::hospital]);
        } else if ($user->hasRole(Role::TEAM_MEMBER)) {
            return in_array($user->member?->team_id, $teamArr) && in_array($emergencyRequest['status'], [EmergencyRequest::helping, EmergencyRequest::complete, EmergencyRequest::incomplete, EmergencyRequest::hospital]);
        }
        return $user->hasRole(Role::ADMIN);
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Role::ADMIN, Role::TEAM_LEADER, Role::TEAM_MEMBER]);
    }

    public function update(User $user, EmergencyRequest $emergencyRequest): bool
    {
        $emergencyRequest->load('emergencyTeams');
        $teamArr = $emergencyRequest->emergencyTeams->pluck('team_id')->toArray();
        if ($emergencyRequest['status'] === EmergencyRequest::waiting) {
            return $user->hasRole(Role::ADMIN);
        } else if ($emergencyRequest['status'] === EmergencyRequest::approve) {
            return $user->hasRole(Role::ADMIN) || ($user->hasRole(Role::TEAM_LEADER) && in_array($user->member?->team_id, $teamArr)); // เช็กด้วยว่าทีมตรงกันไหม
        } else if ($emergencyRequest['status'] === EmergencyRequest::helping) {
            return $user->hasAnyRole([Role::TEAM_LEADER, Role::TEAM_MEMBER]) && in_array($user->member?->team_id, $teamArr); // เช้็กด้วยว่าทีมตรงกันไหม
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

    /**
     * Determine whether the user can view the notification.
     */
    public function viewNoti(User $user, EmergencyRequest $emergencyRequest, ?Notification $notification): bool
    {
        $eventAction = $notification->event_action ?? null;
        $emergencyRequest->load('emergencyTeams');
        $teamArr = $emergencyRequest->emergencyTeams->pluck('team_id')->toArray();

        if ($eventAction === 'created' && ($emergencyRequest['status'] === EmergencyRequest::waiting || $emergencyRequest['status'] === EmergencyRequest::approve)) {
            return $user->hasRole(Role::ADMIN);
        } else if ($eventAction === 'rejected' && $emergencyRequest['status'] === EmergencyRequest::waiting) {
            return $user->hasRole(Role::ADMIN);
        } else if (($eventAction === 'success' && $emergencyRequest['status'] === EmergencyRequest::complete) || ($eventAction === 'unsuccess' && $emergencyRequest['status'] === EmergencyRequest::incomplete)) {
            return $user->hasRole(Role::ADMIN);
        } else if ($eventAction === 'assigned' && $emergencyRequest['status'] === EmergencyRequest::approve) {
            return $user->hasRole(Role::TEAM_LEADER) && in_array($user->member?->team_id, $teamArr);
        } else if ($eventAction === 'assigned' && $emergencyRequest['status'] === EmergencyRequest::helping) {
            return ($user->hasRole(Role::TEAM_MEMBER) || $user->hasRole(Role::TEAM_LEADER)) && in_array($user->member?->team_id, $teamArr);
        }

        return false;
    }
}
