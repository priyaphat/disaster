<?php

namespace App\Http\Controllers\API;

use App\Exceptions\WarningException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\FilterDepartmentRequest;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DepartmentController extends Controller
{
    use AuthorizesRequests;
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    /**
     * Display a listing of the an department | แสดงรายการหน่วยงาน
     */
    public function index(FilterDepartmentRequest $request)
    {
        //
        $paginate = $request->has('paginate');
        $perPage = $request->input('paginate') ?? 10;
        $data = $this->departmentService->getList($request->all(), $paginate, $perPage);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create an department | สร้างหน่วยงาน
     */
    public function store(StoreDepartmentRequest $request)
    {
        //
        $this->authorize('create', Department::class);
        $data = $this->departmentService->create($request->all());
        return ApiResponse::successResponse(__('success.created'), $data);
    }

    /**
     * Show an department | แสดงหน่วยงาน
     */
    public function show(Department $department)
    {
        $this->authorize('view', Department::class);
        return ApiResponse::successResponse(__('success.data'), $department);
    }

    /**
     * Update an department | แก้ไขนัดหมาย
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        //
        $this->authorize('update', Department::class);

        $data = $this->departmentService->update($department, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Delete an department | ลบหน่วยงาน
     */
    public function destroy(Department $department)
    {
        //
        $this->authorize('delete', Department::class);

        $this->departmentService->delete($department);
        return ApiResponse::successResponse(__('success.deleted'));
    }

    /**
     * Check delete an department | ลบหน่วยงาน
     */
    public function checkDelete(Department $department)
    {
        //
        $this->authorize('delete', Department::class);
        $data = $this->departmentService->isUsed($department);
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
