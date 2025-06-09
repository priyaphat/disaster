<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Http\Responses\ApiResponse;
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
    public function store(Request $request)
    {
        // $team = Team::find(35)->load(['leader', 'allUsers']);
        // $team = new TeamResource($team);
        // // return $team;
        // $des = $team;
        // $log = AppointmentLog::create([
        //     'parent_id' => $team->id,
        //     'parent_type' => Team::class,
        //     'description' => $des,
        //     'log_date' => '2025-06-05'
        // ]);
        $apps = Appointment::with('shifts', 'team.allUsers', 'shelter')->get();
        // $team = new TeamResource($team);
        // return $team;
        // $app = $team;

        foreach ($apps as $app) {
            $date = Carbon::parse($app->start_time)->format('Y-m-d');
            $log = AppointmentLog::create([
                'parent_id' => $app->id,
                'parent_type' => Appointment::class,
                'description' => $app,
                'log_date' => $date
            ]);

            $log = AppointmentLog::create([
                'parent_id' => $app->id,
                'parent_type' => AppointmentShift::class,
                'description' => $app->shifts,
                'log_date' => $date
            ]);

            $log = AppointmentLog::create([
                'parent_id' => $app->team->id,
                'parent_type' => Team::class,
                'description' => $app->team,
                'log_date' => $date
            ]);

            $log = AppointmentLog::create([
                'parent_id' => $app->shelter->id,
                'parent_type' => Shelter::class,
                'description' => $app->shelter,
                'log_date' => $date
            ]);

            $log = AppointmentLog::create([
                'parent_id' => $app->team->id,
                'parent_type' => User::class,
                'description' => $app->team->allUsers,
                'log_date' => $date
            ]);
        }
    }
}
