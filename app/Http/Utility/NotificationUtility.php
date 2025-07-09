<?php

namespace App\Http\Utility;

use App\Enums\ApprovalStatus;
use App\Enums\RegistrationType;
use App\Models\ShelterStaff;
use App\Models\TeamMember;
use App\Models\User;

class NotificationUtility
{
    public static function getAllUsers()
    {
        return User::select('id', 'prefix_id', 'firstname', 'lastname')->where(function ($query) {
            $query->where('registration_type', RegistrationType::ADMIN->value)
                ->orWhereHas('approval', function ($query) {
                    $query->where('is_approved', ApprovalStatus::APPROVED->value);
                });
        })->get();
    }

    public static function getUsersInTeam($id)
    {
        return TeamMember::where('team_id', $id)->pluck('user_id');
    }

    public static function getUsersInShelter($id)
    {
        return ShelterStaff::where('shelter_id', $id)->pluck('user_id');
    }
}
