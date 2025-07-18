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
        $now = Carbon::now()->format('Y-m-d');
        $query = Appointment::with(['team.allUsers', 'shelter'])->where('appointment_date', $now);

        if (isset($data['shift'])) {
            $query->where('shift', $data['shift']);
        }

        if (isset($data['team_id'])) {
            $query->where('team_id', $data['team_id'])->doesntHave('teamLog');
        }

        if (isset($data['shelter_id'])) {
            $query->where('shelter_id', $data['shelter_id'])->doesntHave('shelterLog');
        }

        $data = $query->chunkById(100, function ($apps) {
            $apps = AppointmentLogResource::collection($apps)->resolve();
            foreach ($apps as $app) {
                // If has team, save log.
                // If an appointment doesn't have a team, it means the log was saved before the team was deleted.
                if ($app['team']) {
                    $appointmentData = [
                        'appointment_id' => $app['id'],
                        'parentable_id' => $app['id'],
                        'parentable_type' => Appointment::class,
                        'description' => $app,
                        'log_date' => $app['appointment_date']
                    ];
                    self::saveAppointmentLog($appointmentData);

                    $teamData = [
                        'appointment_id' => $app['id'],
                        'parentable_id' => $app['team']['id'] ?? null,
                        'parentable_type' => Team::class,
                        'description' => $app['team'] ?? null,
                        'log_date' => $app['appointment_date']
                    ];
                    self::saveAppointmentLog($teamData);

                    $shelterData = [
                        'appointment_id' => $app['id'],
                        'parentable_id' => $app['shelter']['id'] ?? null,
                        'parentable_type' => Shelter::class,
                        'description' => $app['shelter'] ?? null,
                        'log_date' => $app['appointment_date']
                    ];
                    self::saveAppointmentLog($shelterData);
                }
            }
        });

        Log::info("Save appointment logs");
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
