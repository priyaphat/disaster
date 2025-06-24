<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Department\FilterDepartmentRequest;
use App\Http\Requests\Department\StoreDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Department;
use App\Services\DepartmentService;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }
    /**
     * Display a listing of the resource.
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
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        //
        $data = $this->departmentService->create($request->all());
        return ApiResponse::successResponse(__('success.created'), $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        return ApiResponse::successResponse(__('success.data'), $department);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        //
        $data = $this->departmentService->update($department, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        //
        $this->departmentService->delete($department);
        return ApiResponse::successResponse(__('success.deleted'));
    }
}
