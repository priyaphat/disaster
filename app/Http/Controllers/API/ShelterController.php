<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shelter\CreateShelterRequest;
use App\Http\Requests\Shelter\UpdateShelterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Shelter;
use App\Services\ImageService;
use App\Services\PopulationService;
use App\Services\ShelterService;
use Illuminate\Http\Request;

class ShelterController extends Controller
{
    protected $shelterService, $imageService, $populationService;

    public function __construct(ShelterService $shelterService, ImageService $imageService, PopulationService $populationService)
    {
        $this->shelterService = $shelterService;
        $this->imageService = $imageService;
        $this->populationService = $populationService;
    }

    public function index(Request $request)
    {
        $data = $this->shelterService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(CreateShelterRequest $request)
    {
        $data = $this->shelterService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Shelter::IMAGE_FOLDER, Shelter::class, $data->id);
        return ApiResponse::createdResponse('Success', $data);
    }

    public function update(UpdateShelterRequest $request, Shelter $shelter)
    {
        if (isset($request->status) && (int) $request->status === Shelter::CLOSED) {
            $isShelterClear = $this->populationService->isShelterClear($shelter->id);
            if (!$isShelterClear) {
                return ApiResponse::errorResponse(('ยังมีผู้ประสบภัยอยู่ในศูนย์พักพิงนี้'));
            }
        }

        $data = $this->shelterService->update($shelter, $request->all());
        $data->image = $this->imageService->updateImage($request->image, Shelter::IMAGE_FOLDER, Shelter::class, $data->id);
        return ApiResponse::successResponse(__('success.updated'), $data);
    }

    public function show(Shelter $shelter)
    {
        $data = $this->shelterService->show($shelter);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function utility()
    {
        $data = $this->shelterService->utility();
        return ApiResponse::successResponse(__('success.data'), $data);
    }
}
