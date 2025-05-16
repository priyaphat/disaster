<?php

namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
