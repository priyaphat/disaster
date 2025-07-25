<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTransferRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\Shelter;
use App\Models\ShelterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\SearchService;

class ProductTransferController extends Controller
{

    protected $search;
    public function __construct(SearchService $search)
    {
        $this->search = $search;
    }

    public function shelterList(Request $filter)
    {
        $q = Shelter::select('id', 'name')->where('id', '!=', ProductUtility::getShelter());
        $q = $this->search->search($q, $filter, 'get');
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    public function productList(Request $filter)
    {
        $q = ShelterProduct::select(
            'shelter_products.*',
            'products.unit_type',
            'products.unit_id',
            DB::raw('CONCAT(products.name," (",units.name,")") as name')
        )
            ->where('shelter_products.shelter_id', ProductUtility::getShelter())
            ->where('shelter_products.quantity', '>', 0)
            ->where('products.unit_type', Product::Is_unit)
            ->leftJoin('products', 'products.id', 'shelter_products.product_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->get();

        return ApiResponse::successResponse(__('success.data'), $q);
    }

    public function store(ProductTransferRequest $request)
    {
        $shelter = ProductUtility::getShelter();
        $to = $request['shelter_id'];
        foreach ($request['tranfer'] as $item) {
            //sender
            $pdt = ShelterProduct::where('product_id', $item['product_id'])->where('shelter_id', $shelter)->first();
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
        }
        return ApiResponse::successResponse(__('success.transfer.created'));
    }
}
