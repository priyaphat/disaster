<?php

namespace App\Http\Utility;

use App\Http\Resources\AppointmentLogResource;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Population;
use App\Models\Shelter;
use App\Models\ShelterStaff;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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

    public static function createAppointmentLog(array $data = [])
    {
        $datetime = Carbon::now()->format('Y-m-d H:i');
        $query = Appointment::with(['team.allUsers', 'shelter'])->whereNull('logged');

        if (isset($data['team_id']) || isset($data['shelter_id'])) {
            $query->whereRaw("STR_TO_DATE(CONCAT(appointment_date, ' ', start_time), '%Y-%m-%d %H:%i') <= ?", [$datetime]);
        } else {
            $query->whereRaw("STR_TO_DATE(CONCAT(appointment_date, ' ', end_time), '%Y-%m-%d %H:%i') <= ?", [$datetime]);
        }

        if (isset($data['team_id'])) {
            $query->where('team_id', $data['team_id']);
        }

        if (isset($data['shelter_id'])) {
            $query->where('shelter_id', $data['shelter_id']);
        }

        $data = $query->chunkById(100, function ($apps) {
            foreach ($apps as $app) {
                // If has team, save log.
                // If an appointment doesn't have a team, it means the log was saved before the team was deleted.
                $app['logged'] = true;
                $app->save();

                $log = (new AppointmentLogResource($app))->toArray(request());

                $appointmentData = [
                    'appointment_id' => $log['id'],
                    'parentable_id' => $log['id'],
                    'parentable_type' => Appointment::class,
                    'description' => $log,
                    'log_date' => $log['appointment_date']
                ];
                self::saveAppointmentLog($appointmentData);

                $teamData = [
                    'appointment_id' => $log['id'],
                    'parentable_id' => $log['team']['id'],
                    'parentable_type' => Team::class,
                    'description' => $log['team'],
                    'log_date' => $log['appointment_date']
                ];
                self::saveAppointmentLog($teamData);

                $shelterData = [
                    'appointment_id' => $app['id'],
                    'parentable_id' => $app['shelter']['id'],
                    'parentable_type' => Shelter::class,
                    'description' => $app['shelter'],
                    'log_date' => $app['appointment_date']
                ];
                self::saveAppointmentLog($shelterData);
            }
        });

        Log::info(`Save appointment logs : $datetime`);
    }

    public static function saveAppointmentLog(array $data = [])
    {
        return AppointmentLog::updateOrCreate([
            'appointment_id'   => $data['appointment_id'],
            'parentable_id'   => $data['parentable_id'],
            'parentable_type'   => $data['parentable_type'],
        ], [
            'appointment_id'   => $data['appointment_id'],
            'parentable_id'   => $data['parentable_id'],
            'parentable_type'   => $data['parentable_type'],
            'description'   => $data['description'],
            'log_date'   => $data['log_date'],
        ]);
    }
}
