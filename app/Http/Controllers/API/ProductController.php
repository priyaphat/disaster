<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Services\ImageService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService, $imageService;

    public function __construct(ProductService $productService, ImageService $imageService)
    {
        $this->productService = $productService;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the product | แสดงรายการทรัพยากร
     */
    public function index(Request $request)
    {
        $products = $this->productService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $products);
    }

    public function store(CreateProductRequest $request)
    {
        $data = $this->productService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    public function show(Product $product)
    {
        $product->image = $this->imageService->getByTableAndId(Product::class, $product->id);
        return ApiResponse::successResponse(__('success.data'), $product);
    }
}
