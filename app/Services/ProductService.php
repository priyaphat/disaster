<?php

namespace App\Services;

use App\Http\Controllers\API\ShelterProductController;
use App\Http\Resources\ProductResource;
use App\Http\Utility\ProductUtility;
use App\Models\Product;
use App\Models\ShelterProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getList(array $filters = [], $inside = false, $shelter = null) // inside from shelter list , shelter from (all transfer)
    {
        $shelter =  $shelter ?? ProductUtility::getShelter();
        $data = Product::select(
            'products.*',
            'units.name as unit_name',
            'categories.name as category_name',
            DB::raw('COALESCE(shelter_products.quantity, 0) as quantity'),
            'shelter_products.shelter_id as shelter_id'
        )
            // ->where('products.status', Product::Active) //center shelter all show all status
            ->leftJoin('units', 'products.unit_id', 'units.id')
            ->leftJoin('categories', 'products.category_id', 'categories.id')
            ->leftJoin('shelter_products', function ($join) use ($shelter) {
                $join->on('products.id', '=', 'shelter_products.product_id')
                    ->where('shelter_products.shelter_id', '=', $shelter);
            })
            ->when(
                $inside,
                fn($q) => 
                $q->where('shelter_products.shelter_id', $shelter)
                    ->addSelect(DB::raw("(
                    SELECT r.status
                    FROM product_requests r
                    WHERE r.product_id = products.id
                    AND r.shelter_id = $shelter
                    ORDER BY r.created_at DESC
                    LIMIT 1
                ) as product_request"))
                    ->where('products.status', Product::Active) //center shelter active show status

            )
            ->with('image')
            ->filter($filters);
        if (!empty($filters['request'])) {
            $data = $data->whereNotExists(function ($query) use ($filters, $shelter) {
                $query->select(DB::raw(1))
                    ->from('product_requests as r')
                    ->whereColumn('r.product_id', 'products.id')
                    ->where('r.shelter_id', $shelter)
                    ->orderByDesc('r.created_at')
                    ->limit(1)
                    ->where('r.status', $filters['request']);
            });
        }
        if (isset($filters['paginate']) && $filters['paginate'] && !$inside) {
            $data = $data->paginate($filters['paginate'] ?? 10);
        } else {
            return $data->get();
        }
        return $data->setCollection(ProductResource::collection($data->items())->collection);
    }

    public function create(array $data): Product
    {
        $Product = Product::create($data);
        if (isset($data['quantity']) && $data['quantity'] > 0) {
            $product = new ShelterProduct();
            $product['shelter_id'] = ShelterProduct::Shelter_center;
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
