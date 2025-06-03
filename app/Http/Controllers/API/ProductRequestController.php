<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest\CreateProductReqRequest;
use App\Http\Responses\ApiResponse;
use App\Services\ProductRequestService;
use Illuminate\Http\Request;

class ProductRequestController extends Controller
{
    protected $productRequestService;

    public function __construct(ProductRequestService $productRequestService)
    {
        $this->productRequestService = $productRequestService;
    }

    public function store(CreateProductReqRequest $request)
    {
        $data = $this->productRequestService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }
}
