<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignRoleRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    use AuthorizesRequests;
    //
    /**
     * Assign role to user | เพิ่ม role ให้ผู้ใช้งวาน
     */
    public function store(AssignRoleRequest $request, User $user)
    {
        // $this->authorize('assignRole', User::class);
        $role = Role::find($request->role_id);
        $user->syncRoles($role);
        return ApiResponse::successResponse(__('success.saved'));
    }


    /**
     * Delete role from user | ลบ role ออกจากผู้ใช้งาน
     */
    public function delete(Request $request, User $user)
    {
        // $this->authorize('deleteRole', User::class);
        $user->syncRoles([]);
        return ApiResponse::successResponse(__('success.saved'));
    }
}
