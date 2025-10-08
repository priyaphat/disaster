<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest\UpdateProductReqRequest;
use App\Http\Requests\ProductTransferRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\PaginateUtility;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ProductLogs;
use App\Models\ProductRequest;
use App\Models\Shelter;
use App\Models\ShelterProduct;
use App\Models\StockLogs;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ProductRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SearchService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductTransferController extends Controller
{
    use AuthorizesRequests;
    protected $search;
    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    /**
     * Get All shelter , exception your self.
     */
    public function shelterList(Request $filter)
    {
        // แสดง shelter ที่ สามารถ ย้ายทรัพยากรไปได้
        // $this->authorize('isAdmin', Shelter::class);

        $shelter = ProductUtility::getShelter();
        $q = Shelter::select('id', 'name')->where('id', '!=', $shelter)
            ->where('status', Shelter::OPEN);
        $q = $this->search->search($q, $filter, 'get');
        if ($shelter) {
            $q = $q->prepend([
                'id' => '',
                'name' => 'ศูนย์กลาง',
            ]);
        }
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Get product for tranfer , your shelter
     */
    public function productList(Request $filter, $unit_type = true, $shelter = null) //shelter from (all delete)
    {
        // แสดง ทรัพยากร ที่ shelter มี และสามารถ โอนได้
        $this->authorize('viewAny', ShelterProduct::class);
        return $this->getProductList($filter, $unit_type, $shelter);
    }

    public function getProductList(Request $filter, $unit_type = true, $shelter = null) //shelter from (all delete)
    {
        $shelter = $shelter ?? ProductUtility::getShelter();
        $q = ShelterProduct::select(
            'shelter_products.*',
            'products.unit_type',
            'products.unit_id',
            DB::raw('CONCAT(products.name," ( ",COALESCE(units.name, "ไม่มีหน่วย")," )") as name')
        )
            ->leftJoin('products', 'products.id', 'shelter_products.product_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->where('shelter_products.shelter_id', $shelter)
            ->where('shelter_products.quantity', '>', 0)
            ->where('products.status', Product::Active)
            ->when(
                $unit_type,
                fn($q) =>
                $q->where('products.unit_type', Product::Is_unit)
            )
            ->get();
        if (!$unit_type) return $q;
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Get product_log
     */
    public function productLog(Request $filter)
    {
        // แสดง log การโอนสินค้า ผู้ดูแลศูนย์นั้นๆ ดูได้อย่างเดียว
        $this->authorize('viewAny', ProductLogs::class);

        $shelter = ProductUtility::getShelter();
        $e = ProductLogs::select(
            'product_logs.*',
            DB::raw("COALESCE(`from`.name, 'ศูนย์กลาง') as shelter_name_from"),
            DB::raw("COALESCE(`to`.name, 'ศูนย์กลาง') as shelter_name_to"),
            DB::raw("concat(users.firstname, ' ', users.lastname) as created_name")
        )
            ->where('product_logs.type', ProductLogs::PRODUCT);

        if ($filter['other_shelter']) {
            $e = $e->where(function ($q) use ($shelter) { //and
                $q->where('product_logs.from', '!=', $shelter)
                    ->where('product_logs.to', '!=', $shelter);
            });
        } else {
            $e = $e->where(function ($q) use ($shelter) { //or
                $q->where('from', $shelter)
                    ->orWhere('to', $shelter);
            });
        }

        $e = $e->leftJoin('shelters as from', 'from.id', '=', 'product_logs.from')
            ->leftJoin('shelters as to', 'to.id', '=', 'product_logs.to')
            ->leftJoin('users', 'product_logs.created_by', 'users.id')
            ->when($filter['search'], function ($q) use ($filter) {
                $keyword = $filter['search'];
                $q->where(function ($query) use ($keyword) {
                    $query->where('product_logs.name', 'like', "%$keyword%")
                        ->orWhere(DB::raw("COALESCE(`from`.name, 'ศูนย์กลาง')"), 'like', "%$keyword%")
                        ->orWhere(DB::raw("COALESCE(`to`.name, 'ศูนย์กลาง')"), 'like', "%$keyword%")
                        ->orWhere(DB::raw("concat(users.firstname, ' ', users.lastname)"), 'like', "%$keyword%");
                });
            })
            ->when($filter['start_date'] && $filter['end_date'], fn($q) =>
            $q->whereBetween('product_logs.created_at', [
                Carbon::parse($filter['start_date'])->startOfDay(),
                Carbon::parse($filter['end_date'])->endOfDay()
            ]))
            ->when(
                $filter['unit_id'],
                function ($q) use ($filter) {
                    $keyword = $filter['unit_id'];
                    if ($keyword == -1) $q->whereNull('product_logs.unit_id');
                    else $q->where('product_logs.unit_id', $keyword);
                }
            )
            ->when(
                $filter['category_id'],
                function ($q) use ($filter) {
                    $keyword = $filter['category_id'];
                    if ($keyword == -1) $q->whereNull('product_logs.category_id');
                    else $q->where('product_logs.category_id', $keyword);
                }
            );

        $e = $this->search->search($e, $filter, 'get', [2, 4, 5, 7, 8]);
        $e = PaginateUtility::Paginate($e, $filter['paginate'] ?? 10);
        return ApiResponse::successResponse(__('success.data'), $e);
    }

    /**
     * Created Product Transfer.
     */
    public function store(ProductTransferRequest $request, $shelter = null) //shelter from (all trasfer)
    {
        // โอนย้าย ทรัพยากร
        $this->authorize('create', ShelterProduct::class);
        $shelter = $shelter ?? ProductUtility::getShelter();
        $request['shelter_id'] = $to = ($request['shelter_id'] ?? '') === '' ? 0 : $request['shelter_id'];

        if ($shelter == $to) return ApiResponse::errorResponse(__('auth.unauthenicated_transfer'));
        foreach ($request['transfer'] as $item) {
            //sender
            $pdt = ShelterProduct::select(
                'shelter_products.*',
                'products.unit_id',
                'products.name',
                'products.category_id',
                'products.group_id',
            )
                ->where('product_id', $item['product_id'])->where('shelter_id', $shelter)
                ->leftJoin('products', 'shelter_products.product_id', 'products.id')
                ->first();
            $pdt['quantity'] = $pdt['quantity'] - $item['quantity'];
            $pdt->update();

            //recipint
            $product = ShelterProduct::where('product_id', $item['product_id'])->where('shelter_id', $to)->first();
            $r['spacial'] = isset($r['spacial']) ? $r['spacial'] : false;
            if ($product) {
                $product['quantity'] = $item['quantity'] + $product['quantity'];
                if (!$request['spacial']) $product->update();
            } else {
                $item['shelter_id'] = $to;
                if (!$request['spacial']) ShelterProduct::create($item);
            }
            self::log($request, $item, $pdt);
            (new StockLogController)->store($request, $item, StockLogs::TRANSFEROUT, $pdt, $product);
            //ย้ายแบบ เพิ่ม log ที่ product request log ด้วย
            //ขอ id ของ product_requrest_id
            if ($request['spacial']) {
                $productRequest = ProductRequest::find($request['product_request_id']);
                $resData = [
                    "status" => 1,
                    "request" => $productRequest->request,
                    "quantity" => $item['quantity'],
                    "spacial" => true,
                    "detail" => [
                        "reason" => $item['reason'],
                        "receive" => $item['quantity'],
                        "request" => $productRequest['request'],
                        "quantity" => $product['quantity'],
                        "recipient_id" => $request['shelter_id'] ?? 0,
                        "recipient_name" => ProductUtility::shelterName($request['shelter_id']),
                        "sender_id" => $pdt['shelter_id'],
                        "sender_name" => ProductUtility::shelterName($pdt['shelter_id']),
                    ]
                ];
                $updateProductReqRequest = new UpdateProductReqRequest($resData);
                app(ProductRequestController::class)->update($updateProductReqRequest, $productRequest);
            }
        }
        return ApiResponse::successResponse(__('success.transfer.created'));
    }

    private function log($r, $i, $pdt)
    {
        $i['product'] = Product::select(
            'products.*',
            'categories.name as category_name',
            'units.name as unit_name'
        )
            ->where('products.id', $i['product_id'])
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('units', 'products.unit_id', 'units.id')->first();
        $i['to_shelter_name'] = Shelter::find($r['shelter_id'])->name ?? 'ศูนย์กลาง';
        $i['product']['user_name'] = User::select(DB::raw('concat(firstname," ",lastname) as name'))->where('id', Auth()->user()->id)->value('name');
        $i['reason'] = $i['reason'] ?? null;
        $data = [
            'from' => $pdt['shelter_id'],
            'to' => $r['shelter_id'] ?? 0,
            'detail' => $i,
            'type' => ProductLogs::PRODUCT,
            'name' => $pdt['name'],
            'category_id' => $pdt['category_id'],
            'unit_id' => $pdt['unit_id'],
            'group_id' => $pdt['group_id'],
        ];

        $log = ProductLogs::create($data);

        //noti ใช้ $i['product']
        $notificationService = app(NotificationService::class);
        if ($log->to) {
            $notify = $notificationService->createNotification(
                'shelter_product.received',
                [
                    'shelter' => $i['to_shelter_name'],
                    'product' => $i['product']['name'],
                    'unit' => $i['product']['unit_name'],
                    'amount' => $log['detail']['quantity'],
                ],
                $log->id,
                ProductLogs::class,
                true
            );
            $notificationService->sendToStaff($notify, $log->to);
        } else {
            $shelterName = Shelter::find($pdt['shelter_id'])->name ?? 'ศูนย์กลาง';
            $notify = $notificationService->createNotification(
                'product.received',
                [
                    'shelter' => $shelterName,
                    'product' => $i['product']['name'],
                    'unit' => $i['product']['unit_name'],
                    'amount' => $log['detail']['quantity'],
                ],
                $log->id,
                ProductLogs::class,
                true
            );
            $notificationService->sendToAdmin($notify);
        }
    }
}
