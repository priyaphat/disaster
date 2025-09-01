<?php

namespace App\Http\Utility;

use App\Models\Appointment;
use App\Models\ShelterStaff;

class PermissionUtility
{

    public static function getShelterByTeam($teamId)
    {
        return Appointment::where('team_id', $teamId)->pluck('shelter_id')->value();
    }
}
