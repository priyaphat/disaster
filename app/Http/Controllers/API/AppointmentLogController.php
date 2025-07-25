<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\UpdateAppointmentLogRequest;
use App\Http\Resources\MemberResource;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\User;

class AppointmentLogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function update(UpdateAppointmentLogRequest $request, AppointmentLog $log)
    {
        $teamDescription = $log->description;
        $newUsers = collect($request->user_id);
        $oldUsers = collect($teamDescription['all_members'])->pluck('id');

        $deletedUsers = $oldUsers->diff($newUsers);
        $addedUsers = $newUsers->diff($oldUsers)->values();

        $users = User::whereIn('id', $addedUsers)->get()->keyBy('id');

        $teamMember = collect($teamDescription['all_members'])->reject(fn($user) => in_array($user['id'], $deletedUsers->toArray()));

        foreach ($addedUsers as $user) {
            $userLog = new MemberResource($users[$user]);
            $teamMember->push($userLog);
        }

        $teamDescription['all_members'] = $teamMember;
        $teamDescription['total_members'] = $teamMember->count();

        //  Save new members to team log
        $log->description = $teamDescription;
        $log->save();

        //  Save new members to a appointment log
        $appointmentLog = AppointmentLog::where(['parentable_type' => Appointment::class, 'appointment_id' => $log->appointment_id])->first();
        if ($appointmentLog) {
            if (isset($appointmentLog->description['team'])) {
                $logDescription = $appointmentLog->description;
                $logDescription['team'] = $teamDescription;
                $appointmentLog->description = $logDescription;
                $appointmentLog->save();
            }
        }
    }
}
