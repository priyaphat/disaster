<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Responses\ApiResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\PermissionService;


class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function createRole(PermissionRequest $request)
    {

        $role = $this->permissionService->createRole($request->all());
        return ApiResponse::createdResponse('Success', ['role' => $role]);
    }

    public function showRoles()
    {
        $roles = $this->permissionService->getRoles();
        return ApiResponse::successResponse('Success', ['roles' => $roles]);
    }

    public function showPermissions()
    {
        $permissions = $this->permissionService->getPermissions();
        return ApiResponse::successResponse('Success', ['permissions' => $permissions]);
    }
}
