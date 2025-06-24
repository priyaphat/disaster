<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getList(array $filters = [])
    {
        $data = Product::filter($filters)->with('image', 'unit', 'category')->latest()->paginate($filters['per_page'] ?? 10);
        return $data->setCollection(ProductResource::collection($data->items())->collection);
    }
    
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }
}
