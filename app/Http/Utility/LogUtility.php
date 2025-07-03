<?php

namespace App\Http\Utility;

use App\Models\Appointment;
use App\Models\AppointmentLog;
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
            'population' => Population::class,
            'staff' => ShelterStaff::class,
            'user' => User::class,
            default => Appointment::class,
        };
    }

    public static function createAppointment(array $data = [])
    {
        return AppointmentLog::updateOrCreate([
            'parentable_id'   => $data['parentable_id'],
            'parentable_type'   => $data['parentable_type'],
        ], [
            'parentable_id'   => $data['parentable_id'],
            'parentable_type'   => $data['parentable_type'],
            'description'   => $data['description'],
            'log_date'   => $data['log_date'],
        ]);
    }
}
