<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTransferRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\PaginateUtility;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ProductLogs;
use App\Models\Shelter;
use App\Models\ShelterProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SearchService;
use Carbon\Carbon;

class ProductTransferController extends Controller
{

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
        $q = Shelter::select('id', 'name')->where('id', '!=', ProductUtility::getShelter())
            ->where('status', Shelter::OPEN);
        $q = $this->search->search($q, $filter, 'get');
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Get product for tranfer , your shelter
     */
    public function productList(Request $filter, $unit_type = true, $shelter = null) //shelter from (all delete)
    {
        $shelter = $shelter ?? ProductUtility::getShelter();
        $q = ShelterProduct::select(
            'shelter_products.*',
            'products.unit_type',
            'products.unit_id',
            DB::raw('CONCAT(products.name," (",units.name,")") as name')
        )
            ->leftJoin('products', 'products.id', 'shelter_products.product_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->where('shelter_products.shelter_id', $shelter)
            ->where('shelter_products.quantity', '>', 0)
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
        $shelter = ProductUtility::getShelter();
        $e = ProductLogs::select(
            'product_logs.*',
            DB::raw("COALESCE(`from`.name, 'ศูนย์กลาง') as shelter_name_from"),
            DB::raw("COALESCE(`to`.name, 'ศูนย์กลาง') as shelter_name_to"),
            DB::raw("concat(users.firstname, ' ', users.lastname) as created_name")
        )
            ->where('product_logs.type', ProductLogs::PRODUCT)
            ->where(function ($q) use ($shelter) {
                $q->where('from', $shelter)
                    ->orWhere('to', $shelter);
            })
            ->leftJoin('shelters as from', 'from.id', '=', 'product_logs.from')
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
        $shelter = $shelter ?? ProductUtility::getShelter();
        $to = $request['shelter_id'] ?? '0';
        if ($shelter == $to) return ApiResponse::errorResponse(__('auth.unauthenicated'));
        foreach ($request['transfer'] as $item) {
            //sender
            $pdt = ShelterProduct::select(
                'shelter_products.*',
                'products.unit_id',
                'products.name',
                'products.category_id'
            )
                ->where('product_id', $item['product_id'])->where('shelter_id', $shelter)
                ->leftJoin('products', 'shelter_products.product_id', 'products.id')
                ->first();
            $pdt['quantity'] = $pdt['quantity'] - $item['quantity'];
            $pdt->update();

            //recipint
            $product = ShelterProduct::where('product_id', $item['product_id'])->where('shelter_id', $to)->first();
            if ($product) {
                $product['quantity'] = $item['quantity'] + $product['quantity'];
                $product->update();
            } else {
                $item['shelter_id'] = $to;
                ShelterProduct::create($item);
            }
            self::log($request, $item, $pdt);
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
        $i['to_shelter_name'] = Shelter::find($r['shelter_id'])->name ?? 'ศูยน์กลาง';
        $i['product']['user_name'] = User::select(DB::raw('concat(firstname," ",lastname) as name'))->where('id', Auth()->user()->id)->value('name');
        $data = [
            'from' => $pdt['shelter_id'],
            'to' => $r['shelter_id'] ?? 0,
            'detail' => $i,
            'type' => ProductLogs::PRODUCT,
            'name' => $pdt['name'],
            'category_id' => $pdt['category_id'],
            'unit_id' => $pdt['unit_id']
        ];
        //noti ใช้ $i['product']
        ProductLogs::create($data);
    }
}
