<?php

namespace App\Services;

use App\Models\ProductRequest;

class ProductRequestService
{
    public function create(array $data)
    {
        $products = [];
        foreach ($data['products'] as $product) {
            $products[] = ProductRequest::create([
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'shelter_id' => $data['shelter_id'],
                'status' => ProductRequest::pending,
            ]);
        }

        return $products;
    }
}
