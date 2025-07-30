<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest\CreateProductReqRequest;
use App\Http\Requests\ProductRequest\UpdateProductReqRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\PaginateUtility;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ProductLogs;
use App\Models\ProductRequest;
use App\Services\ProductRequestService;
use App\Services\SearchService;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class ProductRequestController extends Controller
{
    protected $productRequestService;

    public function __construct(ProductRequestService $productRequestService)
    {
        $this->productRequestService = $productRequestService;
    }

    /**
     * Display a listing of product requests| แสดงรายการร้องขอทรัพยากรศูนย์พักพิง | แบบ id ด้วยนะ
     */
    public function index(Request $request)
    {
        $data = $this->productRequestService->index($request->all());
        return ApiResponse::successResponse(__('success.data'), $data);
    }

    /**
     * Create product request| สร้างการร้องขอทรัพยากรศูนย์พักพิง
     */
    public function store(CreateProductReqRequest $request)
    {
        $data = $this->productRequestService->create($request->all());
        return ApiResponse::successResponse(__('success.product_required.created'));
    }

    /**
     * Update product request| อัพเดตการร้องขอทรัพยากรของศูนย์พักพิง
     */
    public function update(UpdateProductReqRequest $request, ProductRequest $productReq)
    {
        if ($productReq['status'] == ProductRequest::STOP) return ApiResponse::errorResponse(__('error.product_required.canNotDelete'));
        if (isset($request['status']) && $request['status'] != $productReq['status'] && $request['status'] == ProductRequest::STOP) {  //ยุติ ส่ง 0 มา 
            $productReq->update(['status' => $request['status']]);
            $pdt = Product::find($productReq['product_id']);

            $this->productRequestService->log($productReq->toArray(), $pdt, ProductLogs::STOP); //แต่ log ส่ง 3 
            return ApiResponse::successResponse(__('success.product_required.stop'), $productReq);
        }
        return $this->productRequestService->update($request->all(), $productReq);
    }

    /**
     * ประวัติการร้องขอ/ยุติ
     */
    public function requestLog(Request $f)
    {
        $q = ProductLogs::select(
            'product_logs.*',
            'units.name as unit_name',
            'categories.name as category_name',
            DB::raw("concat(users.firstname, ' ', users.lastname) as user_name"),
        )
            ->where('product_logs.type', $f['type'])
            ->where('to', ProductUtility::getShelter())
            ->leftJoin('units', 'units.id', 'product_logs.unit_id')
            ->leftJoin('categories', 'categories.id', 'product_logs.category_id')
            ->leftJoin('users', 'users.id', 'product_logs.created_by')
            ->when($f['search'] ?? false, function ($q) use ($f) {
                $search = $f['search'];
                $q->where(function ($q) use ($search) {
                    $q->where('product_logs.name', 'like', "%$search%")
                        ->orWhere(DB::raw("concat(users.firstname, ' ', users.lastname)"), 'like', "%$search%");
                });
            })->when(
                $f['start_date'],
                fn($q) =>
                $q->whereBetween('product_logs.created_at', [
                    Carbon::parse($f['start_date'])->startOfDay(),
                    Carbon::parse($f['end_date'])->endOfDay()
                ])
            );
        $q = app(SearchService::class)->search($q, $f, 'get', [2, 7]);
        return $q;
    }
}
