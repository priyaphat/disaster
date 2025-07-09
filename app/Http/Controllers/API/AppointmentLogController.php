<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShelterResource;
use App\Http\Resources\TeamResource;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\LogUtility;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\AppointmentShift;
use App\Models\Shelter;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;;

use Illuminate\Http\Request;

class AppointmentLogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $logDate = Carbon::now()->format('Y-m-d');
        $apps = Appointment::whereDate('appointment_date', $logDate)->get();
        $teams = TeamResource::collection(Team::with('allUsers')->get());
        $shelters = ShelterResource::collection(Shelter::with('image', 'population', 'leader', 'staff', 'geography')->get());
        foreach ($apps as $app) {
            // Create appointment log
            $appointmentData = [
                'parentable_id' => $app->id,
                'parentable_type' => Appointment::class,
                'description' => $app,
                'log_date' => $logDate
            ];
            LogUtility::createAppointment($appointmentData);
        }

        foreach ($teams as $team) {
            // Create appointment log
            $teamData = [
                'parentable_id' => $team->id,
                'parentable_type' => Team::class,
                'description' => $team,
                'log_date' => $logDate
            ];
            LogUtility::createAppointment($teamData);
        }

        foreach ($shelters as $shelter) {
            // Create appointment log
            $shelterData = [
                'parentable_id' => $shelter->id,
                'parentable_type' => Shelter::class,
                'description' => $shelter,
                'log_date' => $logDate
            ];
            LogUtility::createAppointment($shelterData);
        }
    }
}
