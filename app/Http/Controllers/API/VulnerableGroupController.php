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
     * Display a listing of the an group | แสดงรายการกลุ่มเปราะบาง
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
     * Display a listing of the vulnerable groups | รายการกลุ่มเปราะบาง รวมไม่มีกลุ่มเปราะบาง.
     */
    public function selectable(FilterGroupRequest $request)
    {
        $data = $this->groupService->selectableGroups($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create an group | สร้างกลุ่มเปราะบาง
     */
    public function store(StoreGroupRequest $request)
    {
        //
        $this->authorize('create',Group::class);
        $data = $this->groupService->create($request->all());
        return ApiResponse::successResponse(__('success.created'), $data);
    }

    /**
     * Show an group | แสดงกลุ่มเปราะบาง
     */
    public function show(Group $group)
    {
        $this->authorize('view', Group::class);
        return ApiResponse::successResponse(__('success.data'), $group);
    }

    /**
     * Update an group | แก้ไขนัดหมาย
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        //
        $this->authorize('update', Group::class);

        $data = $this->groupService->update($group, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Delete an group | ลบกลุ่มเปราะบาง
     */
    public function destroy(Group $group)
    {
        //
        $this->authorize('delete',Group::class);
        $this->groupService->delete($group);
        return ApiResponse::successResponse(__('success.deleted'));
    }

    /**
     * Check delete an group | ลบกลุ่มเปราะบาง
     */
    public function checkDelete(Group $group)
    {
        //
        $this->authorize('delete',Group::class);
        $data = $this->groupService->isUsed($group);
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
