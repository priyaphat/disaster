<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTransferRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ProductLogs;
use App\Models\Shelter;
use App\Models\ShelterProduct;
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
        $q = Shelter::select('id', 'name')->where('id', '!=', ProductUtility::getShelter());
        $q = $this->search->search($q, $filter, 'get');
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Get product for tranfer , your shelter
     */
    public function productList(Request $filter)
    {
        $q = ShelterProduct::select(
            'shelter_products.*',
            'products.unit_type',
            'products.unit_id',
            DB::raw('CONCAT(products.name," (",units.name,")") as name')
        )
            ->leftJoin('products', 'products.id', 'shelter_products.product_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->where('shelter_products.shelter_id', 1)
            ->where('shelter_products.quantity', '>', 0)
            ->where('products.unit_type', Product::Is_unit)
            ->get();

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
            ->where('product_logs.from', $shelter)
            ->orWhere('product_logs.to', $shelter)
            ->leftJoin('shelters as from', 'from.id', '=', 'product_logs.from')
            ->leftJoin('shelters as to', 'to.id', '=', 'product_logs.to')
            ->leftJoin('users', 'product_logs.created_by', 'users.id');

        $e = $e->when(
            $filter['search'],
            function ($q) use ($filter) {
                $keyword = $filter['search'];
                $q->where(function ($q) use ($keyword) {
                    $q->orWhere('product_logs.name', 'like', "%$keyword%")
                        ->orWhere('from.name', 'like', "%$keyword%")
                        ->orWhere('to.name', 'like', "%$keyword%")
                        ->orWhere(DB::raw("concat(users.firstname, ' ', users.lastname)"), 'like', "%$keyword%");
                });
            }
        )->when($filter['start_date'] && $filter['end_date'], fn($q) =>
        $q->whereBetween('product_logs.created_at', [
            Carbon::parse($filter['start_date'])->startOfDay(),
            Carbon::parse($filter['end_date'])->endOfDay()
        ]));

        $e = $this->search->search($e, $filter, 'get', [2, 7]);
        return ApiResponse::successResponse(__('success.data'), $e);
    }

    /**
     * Created Product Transfer.
     */
    public function store(ProductTransferRequest $request)
    {
        $shelter = ProductUtility::getShelter();
        $to = $request['shelter_id'];
        foreach ($request['tranfer'] as $item) {
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
        $data = [
            'from' => $pdt['shelter_id'],
            'to' => $r['shelter_id'],
            'detail' => $i,
            'type' => ProductLogs::PRODUCT,
            'name' => $pdt['name'],
            'category_id' => $pdt['category_id'],
            'unit_id' => $pdt['unit_id']
        ];
        ProductLogs::create($data);
    }
}
