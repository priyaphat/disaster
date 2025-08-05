<?php

namespace App\Http\Controllers\API;

use App\Enums\ShelterUtilities;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Responses\ApiResponse;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\Shelter;
use App\Models\ShelterProduct;
use App\Services\ImageService;
use App\Services\ProductService;
use Dedoc\Scramble\Attributes\PathParameter;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Services\SearchService;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
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
        $products = $this->productService->getList($request->all());
        return ApiResponse::successResponse(__('success.data'), $products);
    }

    /**
     * Create a product | สร้างทรัพยากร
     */
    public function store(CreateProductRequest $request)
    {
        $data = $this->productService->create($request->all());
        $data->image = $this->imageService->uploadImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        return self::show($data['id']);
    }

    /**
     * Show specified product| แสดงทรัพยากร
     */
    #[PathParameter('product', description: 'ส่ง id ของ products', type: 'string', example: '3')]
    public function show($product, $val = false)
    {
        $q = Product::select(
            'products.*',
            'shelter_products.quantity',
            'categories.name as category_name',
            'units.name as unit_name'
        )->where('products.id', $product)
            ->leftJoin('shelter_products', 'shelter_products.product_id', 'products.id')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('units', 'products.unit_id', 'units.id')
            ->where('shelter_products.shelter_id', ProductUtility::getShelter())
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
        if ($request['status'] != $product['status']) { // status
            if ($request['status'] == Product::Inactive) {
                if (ShelterProduct::where('product_id', $product->id)->first()) throw new BadRequestHttpException(__('error.product.delete'));
            }
            $product->update($request->all());
            return ApiResponse::successResponse($product->status == Product::Active ? __('success.product.toggle_open') : __('success.product.toggle_close'), $product);
        }
        $data = $this->productService->update($product, $request->all());
        $data->image = $this->imageService->updateImage($request->image, Product::IMAGE_FOLDER, Product::class, $data->id);
        return ApiResponse::successResponse(__('success.product.updated'), self::show($data['id'], true));
    }

    /**
     * Delete the specified product| ลบทรัพยากร
     */
    public function destroy(Product $product)
    {
        // return self::checkDalete($product->id);
        if (ShelterProduct::where('product_id', $product->id)->first()) throw new BadRequestHttpException(__('error.sql.fk-constraint'));
        $product->delete();
        $image = $this->imageService->getByTableAndId(Product::class, $product->id);
        if ($image) {
            $image->delete();
        }
        return ApiResponse::successResponse(__('success.product.deleted'));
    }

    private function checkDalete($productId)
    {
        $q = ShelterProduct::where('product_id', $productId)->get();
        // return $q;
        if($q->isNotEmpty()){
            return 1;
        }
        return 2;
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
            $shelter = Shelter::find($shelter)->value('name');
        }

        if ($res) return ApiResponse::successResponse(__('success.data'), $shelter);
        return $shelter;
    }

    /**
     * change product quantity
     */
    public function quantity(Request $r, $id)
    {
        $r->validate([
            'quantity' => 'required|numeric',
            'condition' => 'required|in:-1,1',
        ]);
        $id = ShelterProduct::where('product_id', $id)->where('shelter_id', ProductUtility::getShelter())->first();
        if ($id['quantity'] == ShelterProduct::MINUS) throw new BadRequestHttpException(__('error.shelter_product.IsNotUnit_error'));
        if ($r['condition'] == ShelterProduct::PLUS) $id['quantity'] += $r['quantity'];
        if ($r['condition'] == ShelterProduct::MINUS) {
            if ($r['quantity'] > $id['quantity']) throw new BadRequestHttpException(__('error.shelter_product.minus_error') . '(' . $id['quantity'] . ')');
            $id->quantity -= $r['quantity'];
        }
        $id->update();
        return ApiResponse::successResponse(($r['condition'] == ShelterProduct::PLUS ? __('success.shelter_product.plus_quantity') : __('success.shelter_product.minus_quantity')), $id);
    }
}
