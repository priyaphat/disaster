<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Responses\ApiResponse;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function store(Request $request)
    {
        $data = $this->permissionService->create($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    // public function createPermission(Request $request)
    // {
    //     $role = $this->permissionService->createPermission($request->all());
    //     return ApiResponse::createdResponse('Success', ['data' => $role]);
    // }

    // public function updateRole()
    // {
    //     $roles = $this->permissionService->getRoles();
    //     return ApiResponse::successResponse('Success', ['roles' => $roles]);
    // }

    public function show()
    {
        $roles = $this->permissionService->getRoles();
        return ApiResponse::successResponse('Success', ['data' => $roles]);
    }

    // public function showPermissions()
    // {
    //     $permissions = $this->permissionService->getPermissions();
    //     return ApiResponse::successResponse('Success', ['permissions' => $permissions]);
    // }
}
