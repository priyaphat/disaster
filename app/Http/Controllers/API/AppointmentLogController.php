<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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
        $logDate = Carbon::now()->format('Y-m-d');
        $apps = Appointment::whereDate('start_time', $logDate)->with('shifts', 'team.allUsers', 'shelter')->get();

        foreach ($apps as $app) {
            // Create appointment log
            AppointmentLog::create([
                'parentable_id' => $app->id,
                'parentable_type' => Appointment::class,
                'description' => $app,
                'log_date' => $logDate
            ]);

            // Create team and mebers log
            $teamLog = AppointmentLog::where(['parentable_id' => $app->team?->id, 'parentable_type' => Team::class, 'log_date' => $logDate])->first();
            if (!$teamLog) {
                AppointmentLog::create([
                    'parentable_id' => $app->team?->id,
                    'parentable_type' => Team::class,
                    'description' => $app->team,
                    'log_date' => $logDate
                ]);

                AppointmentLog::create([
                    'parentable_id' => $app->team->id,
                    'parentable_type' => User::class,
                    'description' => $app->team->allUsers,
                    'log_date' => $logDate
                ]);
            }

            // Create shelter log
            $shelterLog = AppointmentLog::where(['parentable_id' => $app->shelter?->id, 'parentable_type' => Shelter::class, 'log_date' => $logDate])->first();
            if (!$shelterLog) {
                AppointmentLog::create([
                    'parentable_id' => $app->shelter?->id,
                    'parentable_type' => Shelter::class,
                    'description' => $app->shelter,
                    'log_date' => $logDate
                ]);
            }
        }

        return ApiResponse::successResponse(__('success.data'));
    }
}
