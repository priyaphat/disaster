<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function createRole(array $data): Role
    {
        $role = Role::create(['name' => $data['name']]);
        $role->givePermissionTo($data['permissions']);
        return $role;
    }

    public function getRoles()
    {
        return Role::all()->select('id', 'name');
    }

    public function getPermissions()
    {
        return Permission::all()->select('id', 'name');
    }
}
