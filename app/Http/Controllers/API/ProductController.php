<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Services\ImageService;
use App\Services\ProductService;
use Dedoc\Scramble\Attributes\QueryParameter;
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
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "มาม่า")]
    #[QueryParameter('unit_id', description: 'ค้นหาตามหน่วย ดึงหน่วยจาก api unit.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/unit.index', type: 'string', example: "1")]
    #[QueryParameter('category_id', description: 'ค้นหาตามประเภท ดึงประเภทจาก api category.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/category.index', type: 'string', example: "2")]
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

    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $this->productService->update($product, $request->all());
        $data->image = $this->imageService->updateImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return ApiResponse::successResponse(__('success.data'));
    }
}
