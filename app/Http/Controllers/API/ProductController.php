<?php

namespace App\Http\Controllers\API;

use App\Enums\ShelterUtilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Requests\ProductRequest\UpdateProductReqRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ProductRequest;
use App\Models\Shelter;
use App\Models\ShelterProduct;
use App\Services\ImageService;
use App\Services\NotificationService;
use App\Services\ProductService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Services\SearchService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use AuthorizesRequests;
    protected $productService, $imageService, $search;

    public function __construct(ProductService $productService, ImageService $imageService, SearchService $search)
    {
        $this->productService = $productService;
        $this->imageService = $imageService;
        $this->search = $search;
    }

    /**
     * Display a listing of the product | แสดงรายการทรัพยากร กลาง
     */
    #[QueryParameter('per_page', description: 'ส่งจำนวนข้อมูลที่ต้องการต่อหน้า', type: 'string', example: '3')]
    #[QueryParameter('search', description: 'ค้นหาชื่อ', type: 'string', example: "มาม่า")]
    #[QueryParameter('unit_id', description: 'ค้นหาตามหน่วย ดึงหน่วยจาก api unit.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/unit.index', type: 'string', example: "1")]
    #[QueryParameter('category_id', description: 'ค้นหาตามประเภท ดึงประเภทจาก api category.index https://disaster-backend.gentechsoft.tech/api/docs#/operations/category.index', type: 'string', example: "2")]
    #[QueryParameter('sort', description: 'เรียงลำดับการแสดงข้อมูล', type: 'string', example: "asc, desc, name")]
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $products);
    }

    /**
     * Create a product | สร้างทรัพยากร
     */
    public function store(CreateProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $this->productService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        return ApiResponse::successResponse(__('success.product.created'), self::show($data['id'], true));
    }

    /**
     * Show specified product| แสดงทรัพยากร
     */
    #[PathParameter('product', description: 'ส่ง id ของ products', type: 'string', example: '3')]
    public function show($product, $val = false)
    {
        $this->authorize('view', Product::class);

        $q = Product::select(
            'products.*',
            // 'shelter_products.quantity',
            DB::raw('COALESCE(shelter_products.quantity, 0) as quantity'),
            'categories.name as category_name',
            'units.name as unit_name'
        )->where('products.id', $product)
            // ->leftJoin('shelter_products', 'shelter_products.product_id', 'products.id')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('units', 'products.unit_id', 'units.id')
            // ->where('shelter_products.shelter_id', ProductUtility::getShelter())
            ->leftJoin('shelter_products', function ($join) {
                $join->on('shelter_products.product_id', '=', 'products.id')
                    ->where('shelter_products.shelter_id', ProductUtility::getShelter());
            })
            ->first();
        $image = $this->imageService->getByTableAndId(Product::class, $q->id);
        $q->image = $image ? $image['image_path'] : null;
        if ($val) return $q;
        return ApiResponse::successResponse(__('success.data'), $q);
    }

    /**
     * Update the specified product| แก้ไขทรัพยากร
     */
    #[PathParameter('product', description: 'ส่ง id ของ products', type: 'string', example: '3')]
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', Product::class);

        if (isset($request['status'])) {
            if ($request['status'] != $product['status']) { // status
                if ($request['status'] == Product::Inactive) {
                    if (self::checkDalete($product->id, true)) throw new BadRequestHttpException(__('error.product.delete'));
                }
                $product->update($request->all());
                return ApiResponse::successResponse($product->status == Product::Active ? __('success.product.toggle_open') : __('success.product.toggle_close'), $product);
            }
        }
        $data = $this->productService->update($product, $request->all());
        // $data->image = $this->imageService->updateImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        $data->image = $this->imageService->handleImageUpdate($request->all(), Product::IMAGE_FOLDER, Product::class, $data->id);
        return ApiResponse::successResponse(__('success.product.updated'), self::show($data['id'], true));
    }

    /**
     * Delete the specified product| ลบทรัพยากร
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', Product::class);

        if (self::checkDalete($product->id)) throw new BadRequestHttpException(__('error.sql.fk-constraint'));
        ShelterProduct::where('product_id', $product->id)->delete();

        //ยุติ ถ้ามี ร้องขอ
        // ProductRequest::where('product_id', $product->id)->delete();
        $ph = ProductRequest::where('product_id', $product->id)->where('status', Product::Active)->first();
        $r = new UpdateProductReqRequest();
        if ($ph) {
            app(ProductRequestController::class)->update($r->merge(['status' => ProductRequest::STOP]), $ph);
            $ph->delete();
        }

        $product->delete();
        $image = $this->imageService->getByTableAndId(Product::class, $product->id);
        if ($image) {
            $image->delete();
        }
        return ApiResponse::successResponse(__('success.product.deleted'));
    }

    public function checkDel($productId) //true ลบไม่ได้ , false ลบได้
    {
        $this->authorize('delete', Product::class);

        return ApiResponse::successResponse(__('success.data'), (self::checkDalete($productId)));
    }

    private function checkDalete($productId, $excepShelter = false)
    {
        $this->authorize('delete', Product::class);

        $q = ShelterProduct::where('product_id', $productId)
            ->when(
                $excepShelter,
                fn($q) =>
                $q->whereNot('shelter_id', ShelterProduct::Shelter_center)
            )
            ->get();

        if ($q->isNotEmpty()) {
            if (count($q) > 1) return true;
            if ($q[0]['shelter_id'] == ShelterProduct::Shelter_center) {
                if ($q[0]['quantity'] <= 0) return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Get your shelter [name].
     */
    public function yourShelter($res = true)
    {
        $shelter = ProductUtility::getShelter();
        if ($shelter == 0) {
            $shelter = 'ศูนย์กลาง';
        } else {
            $shelter = Shelter::where('id', $shelter)->value('name');
        }

        if ($res) return ApiResponse::successResponse(__('success.data'), $shelter);
        return $shelter;
    }

    /**
     * change product quantity
     */
    public function quantity(Request $r, $id)
    {
        $this->authorize('update', Product::class);

        $r->validate([
            'quantity' => 'required|numeric',
            'condition' => 'required|in:-1,1',
            'shelter_id' => 'required'
        ]);
        $shelter = ProductUtility::getShelter();
        $data = ShelterProduct::where('product_id', $id)->where('shelter_id', $shelter)->first();
        if (!$data && $shelter == ShelterProduct::Shelter_center) {
            $data = ShelterProduct::create([
                'product_id' => $id,
                'shelter_id' => $shelter
            ]);
        } else if (!$data) {
            return ApiResponse::errorResponse(__('error.model_not_found'));
        }

        if ($data['quantity'] == ShelterProduct::MINUS) throw new BadRequestHttpException(__('error.shelter_product.IsNotUnit_error'));
        if ($r['condition'] == ShelterProduct::PLUS) $data['quantity'] += $r['quantity'];
        if ($r['condition'] == ShelterProduct::MINUS) {
            if ($r['quantity'] > $data['quantity']) throw new BadRequestHttpException(__('error.shelter_product.minus_error') . '(' . $data['quantity'] . ')');
            $data->quantity -= $r['quantity'];
        }
        $data->update();
        //noti อัปเดต ทรัพยากรภายในศูนย์ shelter == 0 ทรัพยากรกลาง
        if ($data->shelter_id) {
            $notificationService = app(NotificationService::class);
            $notify = $notificationService->createNotification(
                'shelter_product.updated',
                [
                    'product' => $data->product?->name,
                    'unit' => $data->product?->unit?->name,
                    'total' => $data->quantity,
                ],
                $data->id,
                ShelterProduct::class,
                false
            );
            $notificationService->sendToStaff($notify, $data->shelter_id);
        }
        return ApiResponse::successResponse(($r['condition'] == ShelterProduct::PLUS ? __('success.shelter_product.plus_quantity') : __('success.shelter_product.minus_quantity')), self::show($data['product_id'], true));
    }
}
