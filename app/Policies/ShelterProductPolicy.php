<?php

namespace App\Policies;

use App\Http\Utility\PermissionUtility;
use App\Models\User;

class ShelterProductPolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function isStaff(User $user): bool
    {
        return $user->hasRole('staff');
    }

    private function isTeam(User $user): bool
    {
        return $user->hasAnyRole(['team_leader', 'team_member']);
    }

    private function hasAllowedRole(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function view(User $user): bool
    {
        return $this->hasAllowedRole($user);
    }

    public function viewAny(User $user): bool
    {
        $shelterId = request()->get('id');
        if ($this->isStaff($user)) {
            return $user->staff?->shelter_id == $shelterId;
        }

        if ($this->isTeam($user)) {
            $allowedShelter = PermissionUtility::getShelterByTeam($user->member?->team_id);
            return in_array($shelterId, $allowedShelter);
        }

        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function update(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function delete(User $user): bool
    {
        return $this->isStaff($user);
    }
}
