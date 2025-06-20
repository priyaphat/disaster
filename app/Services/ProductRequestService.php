<?php

namespace App\Services;

use App\Http\Resources\ProductRequestResource;
use App\Models\ProductRequest;

class ProductRequestService
{
    public function index(array $filters = [])
    {
        $data = ProductRequest::filter($filters)->with('product.image', 'product.category', 'product.unit')->latest()->paginate($filters['per_page'] ?? 8);
        return $data->setCollection(ProductRequestResource::collection($data->items())->collection);
    }

    public function create(array $data)
    {
        $products = [];
        foreach ($data['products'] as $product) {
            $products[] = ProductRequest::create([
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'] ?? null,
                'no_quantity' => $product['no_quantity'] ?? null,
                'shelter_id' => $data['shelter_id'],
                'status' => ProductRequest::PENDING,
            ]);
        }

        return $products;
    }

    public function update(array $data, ProductRequest $product_req)
    {
        return $product_req;
    }
}
