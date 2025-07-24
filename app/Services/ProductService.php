<?php

namespace App\Services;

use App\Http\Controllers\API\ShelterProductController;
use App\Http\Resources\ProductResource;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ShelterProduct;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getList(array $filters = [])
    {
        // $data = Product::filter($filters)->with('image', 'unit', 'category')
        // ->latest()->paginate($filters['per_page'] ?? 10);
        // return $data->setCollection(ProductResource::collection($data->items())->collection);

        $data = Product::select(
            'products.*',
            'units.name as unit_name',
            'categories.name as category_name',
            'shelter_products.quantity as quantity'
        )
            ->leftJoin('units', 'products.unit_id', 'units.id')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('shelter_products', function ($join) {
                $join->on('products.id', '=', 'shelter_products.product_id')
                    ->where('shelter_products.shelter_id', '=', ProductUtility::getShelter());
            })
            ->with('image')
            ->filter($filters)
            ->paginate($filters['per_page'] ?? 10);
        return $data->setCollection(ProductResource::collection($data->items())->collection);
    }

    public function create(array $data): Product
    {
        $Product = Product::create($data);
        if (isset($data['quantity'])) {
            $product = new ShelterProduct();
            $product['shelter_id'] = 0;
            $product['quantity'] = $data['quantity'];
            $product['product_id'] = $Product->id;
            $product->save();
        }
        return $Product;
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }
}
