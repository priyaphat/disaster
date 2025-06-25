<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest\CreateProductReqRequest;
use App\Http\Requests\ProductRequest\UpdateProductReqRequest;
use App\Http\Responses\ApiResponse;
use App\Models\ProductRequest;
use App\Services\ProductRequestService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;

class ProductRequestController extends Controller
{
    protected $productRequestService;

    public function __construct(ProductRequestService $productRequestService)
    {
        $this->productRequestService = $productRequestService;
    }

    /**
     * Display a listing of product requests| แสดงรายการร้องขอทรัพยากรศูนย์พักพิง
     */
    #[QueryParameter('per_page', description: 'ส่งจำนวนข้อมูลที่ต้องการต่อหน้า', type: 'string', example: '3')]
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "Shelter 00")]
    #[QueryParameter('shelter_id', description: 'ส่ง shelter_id ของศูนย์พักพิงนั้นๆ', type: 'string', example: "1")]
    #[QueryParameter('unit_id', description: 'ค้นหาตามหน่วย ดึงหน่วยจาก api unit.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/unit.index', type: 'string', example: "1")]
    #[QueryParameter('category_id', description: 'ค้นหาตามประเภท ดึงประเภทจาก api category.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/category.index', type: 'string', example: "2")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    public function index(Request $request) {
        $data = $this->productRequestService->index($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create product request| สร้างการร้องขอทรัพยากรศูนย์พักพิง
     */
    public function store(CreateProductReqRequest $request)
    {
        $data = $this->productRequestService->create($request->all());
        return ApiResponse::createdResponse(__('success.created'), $data);
    }

    /**
     * Update product request| อัพเดตการร้องขอทรัพยากรของศูนย์พักพิง
     */
    #[PathParameter('product_req', description: 'ส่ง ID ของ product_requests นั้นๆ', type: 'string', example: '7')]
    public function update(UpdateProductReqRequest $request, ProductRequest $product_req)
    {
        $data = $this->productRequestService->update($request->all(), $product_req);
        return ApiResponse::createdResponse(__('success.data'), $data);
    }
}
