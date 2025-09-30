<?php

use App\Http\Utility\PermissionUtility;
use App\Models\Role;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return  (int) $user->member?->team_id === (int) $teamId;
});

Broadcast::channel('admin', function ($user) {
    $role = PermissionUtility::getRoleName($user);
    return $role === Role::ADMIN;
});
