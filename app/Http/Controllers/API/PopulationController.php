<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Population\CreatePopulationRequest;
use App\Http\Requests\Population\UpdatePopulationRequest;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Models\Population;
use App\Services\PopulationService;

class PopulationController extends Controller
{
    protected $populationService;

    public function __construct(PopulationService $populationService)
    {
        $this->populationService = $populationService;
    }

    public function index(Request $request)
    {
        $data = $this->populationService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function store(CreatePopulationRequest $request)
    {
        $data = $this->populationService->create($request->all());
        return ApiResponse::createdResponse(__('success.data'), $data);
    }

    public function show(Population $population)
    {
        return ApiResponse::successResponse(__('success.data'), $population);
    }

    public function update(UpdatePopulationRequest $request, Population $population)
    {
        //
        $data = $this->populationService->update($population, $request->all());
        return ApiResponse::successResponse(__('success.updated'), $data);
    }
}
