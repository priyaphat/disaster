<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterType\StoreShelterTypeRequest;
use App\Http\Requests\ShelterType\UpdateShelterTypeRequest;
use App\Http\Requests\ShelterTypeRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ShelterType;
use App\Services\ShelterTypeService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ShelterTypeController extends Controller
{
    //
    use AuthorizesRequests;
    protected $typeService;

    public function __construct(ShelterTypeService $typeService)
    {
        $this->typeService = $typeService;
    }

    /**
     * Display a listing of the shelter types | แสดงรายการประะเภทของศูนย์พักพิง
     */
    public function index(Request $request)
    {
        $data = $this->typeService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    // /**
    //  * Display a listing of the an department | แสดงรายการหน่วยงาน
    //  */
    // public function index(Request $request)
    // {
    //     //
    //     $paginate = $request->has('paginate');
    //     $perPage = $request->input('paginate') ?? 10;
    //     $data = $this->typeService->getList($paginate, $perPage);
    //     return ApiResponse::successResponse(__('success.data'), $data);
    // }

    // /**
    //  * Create an department | สร้างหน่วยงาน
    //  */
    // public function store(StoreShelterTypeRequest $request)
    // {
    //     //
    //     $this->authorize('create', ShelterType::class);
    //     $data = $this->typeService->create($request->all());
    //     return ApiResponse::successResponse(__('success.created'), $data);
    // }

    // /**
    //  * Show an department | แสดงหน่วยงาน
    //  */
    // public function show(ShelterType $shelterType)
    // {
    //     $this->authorize('view', ShelterType::class);
    //     return ApiResponse::successResponse(__('success.data'), $shelterType);
    // }

    // /**
    //  * Update an department | แก้ไขนัดหมาย
    //  */
    // public function update(UpdateShelterTypeRequest $request, ShelterType $shelterType)
    // {
    //     //
    //     $this->authorize('update', ShelterType::class);

    //     $data = $this->typeService->update($shelterType, $request->all());
    //     return ApiResponse::successResponse(__('success.updated'), $data);
    // }

    // /**
    //  * Delete an department | ลบหน่วยงาน
    //  */
    // public function destroy(ShelterType $shelterType)
    // {
    //     //
    //     $this->authorize('delete', ShelterType::class);

    //     $this->typeService->delete($shelterType);
    //     return ApiResponse::successResponse(__('success.deleted'));
    // }
}
