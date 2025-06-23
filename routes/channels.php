<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('Notification.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('notification', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('group.{groupId}', function ($user, $groupId) {
//     // For example, check if $user belongs to group $groupId
//     // Replace this with your actual group membership logic
//     return in_array($user->id, $groupId);
// });