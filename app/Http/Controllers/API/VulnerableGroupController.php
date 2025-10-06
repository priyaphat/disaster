<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\VulnerableGroup\FilterGroupRequest;
use App\Http\Requests\VulnerableGroup\StoreGroupRequest;
use App\Http\Requests\VulnerableGroup\UpdateGroupRequest;
use App\Http\Responses\ApiResponse;
use App\Models\VulnerableGroup as Group;
use App\Services\VulnerableGroupService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class VulnerableGroupController extends Controller
{
    //
    use AuthorizesRequests;
    protected $groupService;

    public function __construct(VulnerableGroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Display a listing of the an department | แสดงรายการหน่วยงาน
     */
    public function index(FilterGroupRequest $request)
    {
        //
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->groupService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create an department | สร้างหน่วยงาน
     */
    public function store(StoreGroupRequest $request)
    {
        //
        $this->authorize('create',Group::class);
        $data = $this->groupService->create($request->all());
        return ApiResponse::successResponse(__('success.created'), $data);
    }

    /**
     * Show an department | แสดงหน่วยงาน
     */
    public function show(Group $group)
    {
        $this->authorize('view', Group::class);
        return ApiResponse::successResponse(__('success.data'), $group);
    }

    /**
     * Update an department | แก้ไขนัดหมาย
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        //
        $this->authorize('update', Group::class);

        $data = $this->groupService->update($group, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Delete an department | ลบหน่วยงาน
     */
    public function destroy(Group $group)
    {
        //
        $this->authorize('delete',Group::class);
        $this->groupService->delete($group);
        return ApiResponse::successResponse(__('success.deleted'));
    }

    /**
     * Check delete an department | ลบหน่วยงาน
     */
    public function checkDelete(Group $group)
    {
        //
        $this->authorize('delete',Group::class);
        $data = $this->groupService->isUsed($group);
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
