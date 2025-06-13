<?php

namespace App\Http\Utility;

use App\Models\Appointment;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterStaff;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;

class LogUtility
{
    public static function getClassName($class)
    {
        return match ($class) {
            'team' => Team::class,
            'team_member' => TeamMember::class,
            'shelter' => Shelter::class,
            'staff' => ShelterStaff::class,
            'population' => Population::class,
            'user' => User::class,
            default => Appointment::class,
        };
    }
}
