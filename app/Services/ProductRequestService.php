<?php

namespace App\Services;

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProductRequestController;
use App\Http\Controllers\API\ShelterProductController;
use App\Http\Requests\ProductRequest\UpdateProductReqRequest;
use App\Http\Requests\ShelterProductRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ProductLogs;
use App\Models\ProductRequest;
use App\Models\ShelterProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProductRequestService
{
    public function index($f)
    {
        $q = ProductRequest::select(
            'product_requests.*',
            DB::raw('(product_requests.request - product_requests.quantity) as needed'),
            'products.name as name',
            'products.unit_id',
            'products.category_id',
            'units.name as unit_name',
            'categories.name as category_name'
        )
            ->where('product_requests.status', ProductRequest::ACTIVE)
            ->where('product_requests.shelter_id', ProductUtility::getShelter())
            ->leftJoin('products', 'products.id', 'product_requests.product_id')
            ->leftJoin('units', 'units.id', 'products.unit_id')
            ->leftJoin('categories', 'categories.id', 'products.category_id');

        if (isset($f['id'])) {
            return $q->where('product_requests.id', $f['id'])->first();
        }
        return (new SearchService)->search($q, $f, 'get', [3]);
    }


    public function create($r)
    {
        $shelter = ProductUtility::getShelter();
        foreach ($r as $i) {
            $already = ShelterProduct::where('product_id', $i['product_id'])->where('shelter_id', $shelter)->first();
            if ($already) {
                self::create_request($i, $shelter);
            } else { //if not have shelter product | this create. 
                $shelter_product = new ShelterProductRequest();
                $item['product_id'] = $i['product_id'];
                $item['quantity'] = 0;
                (new ShelterProductController)->store($shelter_product->merge($item));
                self::create_request($i, $shelter);
            }
        }
    }

    public function create_request($r, $shelter)
    {
        $r['shelter_id'] = $shelter;
        $product = Product::find($r['product_id']);
        ProductRequest::create($r);

        self::log($r, $product, ProductLogs::REQUEST);
    }

    public function update($r, ProductRequest $productReq)
    {

        //การ เปลี่ยน type ขอจำนวน ไม่สามารถ ถ้า quantity (ได้รับ) > 0
        if ($r['request'] != $productReq['request'] && $r['request'] == ProductRequest::NOT_QUANTITY && $productReq['quantity'] > 0) {
            // throw new BadRequestHttpException(__('error.product_required.not_change_type_receive'));
            return ApiResponse::errorResponse(__('error.product_required.not_change_type_receive'));
        }
        //หากลดจำนวนที่ต้องการ ลงและมียอดรับแล้ว ไม่สามารถ ลดได้กว่า จำนวนที่รับมาแล้ว
        if ($productReq['quantity'] > 0 && ($r['request'] <= $productReq['quantity']) && $r['request'] != ProductRequest::NOT_QUANTITY) {
            return ApiResponse::errorResponse(__('error.product_required.minus_quantity'));
        }

        $log_stop = false;
        //หากมีการ รับทรัพยากร
        if (isset($r['quantity'])) {
            //add quantity
            $q = ShelterProduct::where('product_id', $productReq['product_id'])->where('shelter_id', $productReq['shelter_id'])->first();
            // if quantity more receive
            if ($productReq['quantity'] + $r['quantity'] < $productReq['request']) {
                $q['quantity'] += $r['quantity'];
                $productReq['quantity'] += $r['quantity'];
            } else {
                $q['quantity'] += $productReq['request'] - $productReq['quantity'];
                $productReq['quantity'] = $productReq['request'];
                //log stop
                $log_stop = true;
            }
            $q->update();
            //log
            $pdt = Product::find($productReq->product_id);
            self::log($productReq, $pdt, ProductLogs::RECEIVE);
            $stop = new UpdateProductReqRequest();
            if ($log_stop) app(ProductRequestController::class)->update($stop->merge(['status' => 0]), $productReq);
        }

        $productReq->update();

        return ApiResponse::successResponse(__('success.product_required.updated'));
    }

    public function log($detail, $pdt, $type)
    {
        $data = [
            'to' => $detail['shelter_id'],
            'detail' => $detail,
            'type' => $type,
            'name' => $pdt['name'],
            'category_id' => $pdt['category_id'],
            'unit_id' => $pdt['unit_id']
        ];
        ProductLogs::create($data);
    }
}
