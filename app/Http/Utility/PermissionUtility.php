<?php

namespace App\Http\Utility;

use App\Models\Appointment;
use App\Models\Role;

class PermissionUtility
{
    public static function getShelterByTeam($teamId)
    {
        return Appointment::where('team_id', $teamId)->whereNull('logged')->distinct()->pluck('shelter_id');
    }

    public static function getRoleName($user)
    {
        return $user->getRoleNames()->first() ?? null;
    }

    public static function getPopulationFilters($user)
    {
        if ($user->hasRole(Role::STAFF)) {
            return ['shelter_id' => $user->staff?->shelter_id];
        }

        return [];
    }
}
