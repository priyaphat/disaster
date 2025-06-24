<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Product;
use App\Services\ImageService;
use App\Services\ProductService;
use Dedoc\Scramble\Attributes\PathParameter;
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
    #[QueryParameter('per_page', description: 'ส่งจำนวนข้อมูลที่ต้องการต่อหน้า', type: 'string', example: '3')]
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "มาม่า")]
    #[QueryParameter('unit_id', description: 'ค้นหาตามหน่วย ดึงหน่วยจาก api unit.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/unit.index', type: 'string', example: "1")]
    #[QueryParameter('category_id', description: 'ค้นหาตามประเภท ดึงประเภทจาก api category.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/category.index', type: 'string', example: "2")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    public function index(Request $request)
    {
        $products = $this->productService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $products);
    }

    /**
     * Create a product | สร้างทรัพยากร
     */
    public function store(CreateProductRequest $request)
    {
        $data = $this->productService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Show specified product| แสดงทรัพยากร
     */
    #[PathParameter('product', description: 'ส่ง id ของ products', type: 'string', example: '3')]
    public function show(Product $product)
    {
        $product->image = $this->imageService->getByTableAndId(Product::class, $product->id);
        return ApiResponse::successResponse(__('success.data'), $product);
    }

    /**
     * Update the specified product| แก้ไขทรัพยากร
     */
    #[PathParameter('product', description: 'ส่ง id ของ products', type: 'string', example: '3')]
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $this->productService->update($product, $request->all());
        $data->image = $this->imageService->updateImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Delete the specified product| ลบทรัพยากร
     */
    #[PathParameter('product', description: 'ส่ง id ของ products', type: 'string', example: '3')]
    public function destroy(Product $product)
    {
        $product->delete();
        return ApiResponse::successResponse(__('success.data'));
    }
}
