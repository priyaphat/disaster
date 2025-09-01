<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff']);
    }

    public function viewList(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff', 'team_leader', 'team_member']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, User $userUpdated): bool
    {
        return $user->hasRole('admin') || $user->id == $userUpdated->id;
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function approve(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function transfer(User $user): bool
    {
        return $user->hasRole('admin');
    }

    // public function view(User $user): bool
    // {
    //     return $user->hasPermissionTo('view-users');
    // }

    //  public function viewList(User $user): bool
    // {
    //     return $user->hasPermissionTo('update-appointment-logs') || $user->hasPermissionTo('create-teams') || $user->hasPermissionTo('update-teams');
    // }

    // public function create(User $user): bool
    // {
    //     return $user->hasPermissionTo('create-users');
    // }

    // public function update(User $user): bool
    // {
    //     return $user->hasPermissionTo('update-users');
    // }

    // public function delete(User $user): bool
    // {
    //     return $user->hasPermissionTo('delete-users');
    // }
}
