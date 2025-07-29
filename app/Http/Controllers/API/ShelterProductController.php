<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShelterProductRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\ShelterProduct;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\ProductTransferRequest;
use App\Models\Product;
use App\Services\SearchService;

class ShelterProductController extends Controller
{
    /**
     * Get shelter product | ภายใน ศูนย์
     */
    public function index(Request $filter)
    {
        $q = (new ProductService)->getList($filter->all(), true);
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Delete shelter all-product | ภายใน ศูนย์
     */
    public function delete()
    {
        $q = (new ProductService)->getList([], true); //ถ้าไม่อยาก loop ก็ใช้ transfer product list
        foreach ($q as $i) if ($i['quantity'] > 0) return ApiResponse::errorResponse(__('error.product_transfer.please_transfer'));
        ShelterProduct::where('shelter_id', ProductUtility::getShelter())->delete();
        return ApiResponse::successResponse(__('success.shelter_product.deleted_all'));
    }

    /**
     * transfer shelter all-product | ภายใน ศูนย์
     */
    public function transfer()
    {
        $q = (new ProductService)->getList([], true);

        $r = new ProductTransferRequest();
        $item['shelter_id'] = ShelterProduct::Shelter_center;
        foreach ($q as $i) {
            $item['tranfer'][] = [
                'product_id' => $i['id'],
                'quantity' => $i['quantity']
            ];
        }
        $r->merge($item);
        return app(ProductTransferController::class)->store($r);
    }

    /**
     * Get list exception product add to shelter
     */
    public function productException(Request $r)
    {
        $already = ShelterProduct::where('shelter_id', ProductUtility::getShelter())->get()->pluck('product_id');
        $q = Product::whereNotIn('id', $already);
        $q = (new SearchService)->search($q, $r, 'get');
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Create shelter product | ภายใน ศูนย์
     */
    public function store(ShelterProductRequest $r)
    {
        $r = $r->all();
        $r['shelter_id'] = ProductUtility::getShelter();
        $q = ShelterProduct::create($r);
        return ApiResponse::successResponse(__('success.shelter_product.created'), $q);
    }
}
