<?php

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use App\Models\Shelter;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return $user->team_id == $teamId;
});

Broadcast::channel('admin', function ($user) {
    $role = PermissionUtility::getRoleName($user);
    return $role === Role::ADMIN;
});
