<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterStaff\CreateShelterStaffRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ShelterService;
use App\Services\ShelterStaffService;
use Illuminate\Http\Request;

class ShelterStaffController extends Controller
{
    protected $shelterService, $shelterstaffService;

    public function __construct(ShelterService $shelterService, ShelterStaffService $shelterstaffService)
    {
        $this->shelterService = $shelterService;
        $this->shelterstaffService = $shelterstaffService;
    }

    public function index(Request $request)
    {
        $data = $this->shelterstaffService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(CreateShelterStaffRequest $request)
    {
        $data = $this->shelterstaffService->create($request->all());
        return ApiResponse::createdResponse('Success', $data);
    }
}
