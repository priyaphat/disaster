<?php

namespace App\Http\Controllers\API;

use App\Enums\ProductGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest\UpdateProductReqRequest;
use App\Http\Requests\ShelterProductRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\ShelterProduct;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Http\Requests\ProductTransferRequest;
use App\Http\Utility\PaginateUtility;
use App\Models\Product;
use App\Models\ProductRequest;
use App\Services\SearchService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Response;

class ShelterProductController extends Controller
{
    use AuthorizesRequests;
    /**
     * Get shelter product | ภายใน ศูนย์
     */
    public function index(Request $filter, $shelter = null)
    {

        $this->authorize('viewAny', ShelterProduct::class);

        // ดัก เข้าไปดู shelter ที่ไม่ใช่ของตัวเอง
        $shelterSelf = $shelter ?? ProductUtility::getShelter();
        if ($shelterSelf != $shelter) return Response()->json(['success' => true, "code" => 200, 'message' => __('success.data'), 'quantity' => 0]);

        $shelter = $shelter ?? $shelterSelf;
        // $q = (new ProductService)->getList($filter->all(), true, $shelter);
        $q = (new ProductService)->getList($filter->all(), true, $shelter)
            ->map(function ($item) {
                $item->group_name = ProductGroup::tryFrom($item->group_id)->label();
                return $item;
            });
        $quantity =  $q->sum('quantity');
        if ($filter['paginate']) $q = PaginateUtility::Paginate($q, $filter['paginate'] ?? 10);
        return Response()->json(['success' => true, "code" => 200, 'message' => __('success.data'), 'data' => $q, 'quantity' => $quantity]);
        // return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Delete shelter all-product | ภายใน ศูนย์
     */
    public function delete($shelter = null)
    {
        $this->authorize('delete', ShelterProduct::class);

        $q = app(ProductTransferController::class)->getProductList(new Request(), false, $shelter);
        if (count($q) > 0) return ApiResponse::errorResponse(__('error.product_transfer.please_transfer'));
        $shelter = $shelter ?? ProductUtility::getShelter();

        //ส่งยุติ product req เก็บ log ถ้ามี
        $pr = ProductRequest::where('shelter_id', $shelter)->where('status', Product::Active)->get();
        if ($pr->isNotEmpty()) {
            $r = new UpdateProductReqRequest();
            $r->merge(['status' => ProductRequest::STOP]);
            foreach ($pr as $i) {
                app(ProductRequestController::class)->update($r, $i);
            }
        }
        ProductRequest::where('shelter_id', $shelter)->delete();
        ShelterProduct::where('shelter_id', $shelter)->delete();

        return ApiResponse::successResponse(__('success.shelter_product.deleted_all'));
    }

    /**
     * Delete onec product on shelter | ลบ ทรัพยากรภายในศูนย์
     */
    public function deleteOne(Request $p, $productId)
    {
        $this->authorize('delete', ShelterProduct::class);

        $shelter = ProductUtility::getShelter();
        $q = ShelterProduct::where('product_id', $productId)->where('shelter_id', $shelter)
            ->where('quantity', '>', 0)->first();
        if ($p['check']) { //true ลบไม่ได้ , false ลบได้ || ส่ง check = 1 for check can delete data
            if ($q) return ApiResponse::successResponse(__('error.product_transfer.please_transfer_one'), true);
            return ApiResponse::successResponse(__('success.data'), false);
            // return ApiResponse::errorResponse(__('error.product_transfer.please_transfer'));
        }
        if ($q) return ApiResponse::errorResponse(__('error.product_transfer.please_transfer_one'));
        //ยุติ ถ้ามี ร้องขอ
        $ph = ProductRequest::where('product_id', $productId)->where('shelter_id', $shelter)
            ->where('status', Product::Active)->first();
        $r = new UpdateProductReqRequest();
        if ($ph) {
            app(ProductRequestController::class)->update($r->merge(['status' => ProductRequest::STOP]), $ph);
            $ph->delete();
        }
        ShelterProduct::where('product_id', $productId)->where('shelter_id', $shelter)->delete();
        return ApiResponse::successResponse(__('success.shelter_product.deleted_all'));
    }

    /**
     * transfer shelter all-product | ภายใน ศูนย์
     */
    public function transfer($shelter = null)
    {
        $this->authorize('update', ShelterProduct::class);

        $q = (new ProductService)->getList([], true, $shelter);
        $r = new ProductTransferRequest();
        // $item['shelter_id'] = ShelterProduct::Shelter_center;
        $item['shelter_id'] = 0;
        $item['transfer'] = [];
        foreach ($q as $i) {
            if ($i['quantity'] > 0) {
                $item['transfer'][] = [
                    'product_id' => $i['id'],
                    'quantity' => $i['quantity']
                ];
            }
        }
        $r->merge($item);
        return app(ProductTransferController::class)->store($r, $shelter);
    }

    /**
     * Get list exception product add to shelter
     */
    public function productException(Request $r)
    {
        $already = ShelterProduct::where('shelter_id', ProductUtility::getShelter())->get()->pluck('product_id');
        $q = Product::whereNotIn('id', $already)
            ->where('status', Product::Active)
            ->orderBy('name', $r['sort'] ?? 'asc');
        $q = (new SearchService)->search($q, $r, 'get', [6]);
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Create shelter product | ภายใน ศูนย์
     */
    public function store(ShelterProductRequest $r)
    {
        $this->authorize('create', ShelterProduct::class);
        $r = $r->all();
        $r['shelter_id'] = ProductUtility::getShelter();
        if (ShelterProduct::where('product_id', $r['product_id'])->where('shelter_id', $r['shelter_id'])->first()) return ApiResponse::errorResponse(__('error.product_required.add_unique'));
        $q = ShelterProduct::create($r);
        return ApiResponse::successResponse(__('success.shelter_product.created'), $q);
    }
}
