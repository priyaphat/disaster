<?php

namespace App\Http\Requests;

use App\Http\Utility\ProductUtility;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\ShelterProduct;

class ProductTransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shelter_id' => 'required|exists:shelters,id',
            'tranfer' => 'required|array',
            'tranfer.*.product_id' => 'required|exists:products,id',
            'tranfer.*.quantity' => 'required|numeric|min:1',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $shelter = ProductUtility::getShelter();

            foreach ($this->input('tranfer', []) as $index => $item) {
                $product = ShelterProduct::where('product_id', $item['product_id'])
                    ->where('shelter_id', $shelter)
                    ->first();

                $available = $product?->quantity ?? 0;
                $requested = $item['quantity'] ?? 0;

                if ($requested > $available) {
                    $validator->errors()->add("tranfer.$index.quantity", "จำนวนมากกว่าที่มีในคลัง ($available)");
                }
            }
        });
    }
}
