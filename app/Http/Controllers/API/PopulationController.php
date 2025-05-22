<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Population\PopulationCreateRequest;
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

    public function store(PopulationCreateRequest $request)
    {
        $population = $this->populationService->create($request->all());
        return ApiResponse::createdResponse('Success', $population);
    }

    public function getPopulation(Population $population)
    {
        $population = $this->populationService->getPopulation($population->id);
        return ApiResponse::successResponse('Success', $population);
    }
}
