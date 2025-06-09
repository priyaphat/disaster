<?php

namespace App\Http\Utility;

use App\Http\Resources\TeamResource;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Shelter;
use App\Models\Team;
use App\Models\TeamMember;
use Carbon\Carbon;

class LogUtillity
{

    /**
     * Store a newly created resource in storage.
     */
    public static function getData($id, $date, $class = null)
    {
        $className = self::getClassName($class);
        $logDate = Carbon::parse($date)->format('Y-m-d');
        $log = AppointmentLog::where(['parent_id' => $id, 'parent_type' => $className])->whereDate('log_date', $logDate)->first();
        if ($log) {
            $data = json_decode($log->description);
            return $data;
        }
        return null;
    }

    /**
     * Get the table name based on the folder.
     *
     * @param string $folder
     * @return string
     */
    private static function getClassName($class)
    {
        return match ($class) {
            'team' => Team::class,
            'team_member' => TeamMember::class,
            'shelter' => Shelter::class,
            default => Appointment::class,
        };
    }
}
