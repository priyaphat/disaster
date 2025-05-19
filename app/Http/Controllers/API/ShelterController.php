<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shelter\ShelterCreateRequest;
use App\Http\Requests\Shelter\ShelterUpdateRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Shelter;
use App\Services\ShelterService;

class ShelterController extends Controller
{
    protected $shelterService;

    public function __construct(ShelterService $shelterService)
    {
        $this->shelterService = $shelterService;
    }

    public function store(ShelterCreateRequest $request)
    {
        $data = $this->shelterService->create($request->all());

        return ApiResponse::createdResponse('Success', $data);
    }

    public function update(ShelterUpdateRequest $request, Shelter $shelter)
    {
        $data = $this->shelterService->update($shelter, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
