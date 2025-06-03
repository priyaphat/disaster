<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shelter\CreateShelterRequest;
use App\Http\Requests\Shelter\UpdateShelterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Shelter;
use App\Services\ShelterService;
use Illuminate\Http\Request;

class ShelterController extends Controller
{
    protected $shelterService;

    public function __construct(ShelterService $shelterService)
    {
        $this->shelterService = $shelterService;
    }

    public function index(Request $request)
    {
        $data = $this->shelterService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(CreateShelterRequest $request)
    {
        $data = $this->shelterService->create($request->all());

        return ApiResponse::createdResponse('Success', $data);
    }

    public function update(UpdateShelterRequest $request, Shelter $shelter)
    {
        $data = $this->shelterService->update($shelter, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
