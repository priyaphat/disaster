<?php

namespace App\Services;

use App\Http\Resources\MemberResource;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\User;

class AppointmentLogService
{
    /**
     * Update an appointment log
     */
    public function update(AppointmentLog $log, array $data)
    {
        $teamDescription = $log->description;

        $existingUsers = collect($teamDescription['all_members'])->pluck('id')->values();
        $oldUsers = collect($data['old_users'])->values();

        $deletedUsers = $existingUsers->diff($oldUsers);
        $addedUsers = $data['new_users'] ?? [];

        $users = User::whereIn('id', $addedUsers)->get()->keyBy('id');

        $teamMember = collect($teamDescription['all_members'])->reject(fn($user) => in_array($user['id'], $deletedUsers->toArray()));

        foreach ($addedUsers as $user) {
            $userLog = new MemberResource($users[$user]);
            $teamMember->push($userLog);
        }

        $teamDescription['all_members'] = $teamMember;
        $teamDescription['total_members'] = $teamMember->count();

        // Save new members to team log
        $log->description = $teamDescription;
        $log->save();

        // Save new members to a appointment log
        $appointmentLog = AppointmentLog::where(['parentable_type' => Appointment::class, 'appointment_id' => $log->appointment_id])->first();
        if ($appointmentLog) {
            if (isset($appointmentLog->description['team'])) {
                $logDescription = $appointmentLog->description;
                $logDescription['team'] = $teamDescription;
                $appointmentLog->description = $logDescription;
                $appointmentLog->save();
            }
        }

        return $log;
    }
}
