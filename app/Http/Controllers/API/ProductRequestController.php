<?php

namespace App\Http\Controllers\API;

use App\Enums\ProductLogs as EnumsProductLogs;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest\CreateProductReqRequest;
use App\Http\Requests\ProductRequest\UpdateProductReqRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\PaginateUtility;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ProductLogs;
use App\Models\ProductRequest;
use App\Models\ShelterProduct;
use App\Services\ProductRequestService;
use App\Services\SearchService;
use Carbon\Carbon;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * Display a listing of product requests on Dashboard| แสดงรายการร้องขอทรัพยากรศูนย์พักพิง บนหน้า แดชบอร์ด
     */
    public function indexPublic(Request $f, $RequsetId = null)
    {
        $q = ProductRequest::select(
            'product_requests.product_id',
            DB::raw('SUM(product_requests.quantity) as total_quantity'),
            DB::raw('SUM(product_requests.request) as total_request'),
            //  DB::raw('SUM(CASE WHEN product_requests.request != -1 THEN product_requests.request ELSE 0 END) as total_request'),
            DB::raw('MAX(product_requests.created_at) as created_at'),
            DB::raw('MAX(product_requests.id) as id'),
            DB::raw('(SUM(product_requests.request) - SUM(product_requests.quantity)) as needed'),
            //         DB::raw('(
            //     SUM(CASE WHEN product_requests.request != -1 THEN product_requests.request ELSE 0 END) 
            //     - SUM(product_requests.quantity)
            // ) as needed'),
            'products.name as name',
            'products.unit_id',
            'products.category_id',
            'units.name as unit_name',
            'categories.name as category_name'
        )
            ->leftJoin('products', 'products.id', 'product_requests.product_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->leftJoin('categories', 'categories.id', 'products.category_id')
            ->with('product.image')
            ->groupBy('product_requests.product_id')
            ->when(
                $RequsetId,
                fn($q) =>
                $q->where('product_requests.product_id', $RequsetId)
            )
            ->when(
                $f['category_id'],
                function ($q) use ($f) {
                    if ($f['category_id'] == -1) $q->whereNull('products.category_id');
                    else $q->where('products.category_id', $f['category_id']);
                }
            )->when(
                $f['sort'],
                fn($q) =>
                $q->orderByRaw('MAX(product_requests.created_at) ' . ($f['sort'] == 'asc' ? 'asc' : 'desc'))
            );

        if ($RequsetId) {
            $q = $q->first();
        } else {
            $q = $q->paginate($f['paginate'] ?? 10);
        }

        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Display a listing of product requests on Dashboard Detail| แสดงรายการร้องขอทรัพยากรศูนย์พักพิง บนหน้า แดชบอร์ด
     */
    public function indexPublicDetail(Request $f, $RequsetId)
    {
        $q = ProductRequest::select(
            'product_requests.created_at',
            'product_requests.shelter_id',
            'shelters.name',
            'shelters.phone',
            DB::raw('(product_requests.request - product_requests.quantity) as needed'),
        )
            ->where('product_id', $RequsetId)
            ->when(
                $f['search'],
                fn($q) =>
                $q->where('shelters.name', 'like', '%' . $f['search'] . '%')
            )
            ->leftJoin('shelters', 'shelters.id', 'product_requests.shelter_id')
            ->paginate($f['paginate'] ?? 10);
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Create product request| สร้างการร้องขอทรัพยากรศูนย์พักพิง
     */
    public function store(CreateProductReqRequest $request)
    {
        if (ProductUtility::getShelter() == ShelterProduct::Shelter_center) throw new BadRequestHttpException('ผู้ใช้งานดังกล่าว ยังไม่ได้สังกัดในศูนย์พักพิงใด');
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
            $this->productRequestService->log($productReq, $pdt, ProductLogs::STOP); //แต่ log ส่ง 3 
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
            ->when(
                $f['type'],
                function ($q) use ($f) {
                    $q->where('product_logs.type', $f['type']);
                }
            )
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
        $q = app(SearchService::class)->search($q, $f, 'get', [2, 7, 8]);

        $q = $q->toArray();
        $add_type = array_map(function ($i) {
            $enum = EnumsProductLogs::tryFrom($i['type']);
            $i['type_name'] = $enum?->label(); // null-safe: ถ้าไม่มีค่า จะได้ null
            return $i;
        }, $q);

        if ($f['paginate']) return PaginateUtility::Paginate($add_type, $f['paginate'] ?? 10);
        return $add_type;
    }
}
