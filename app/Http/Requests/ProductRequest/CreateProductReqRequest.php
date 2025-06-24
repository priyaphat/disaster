<?php

namespace App\Http\Requests\ProductRequest;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductReqRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * Array ของทรัพยากรที่ร้องขอ
             * @var array
             * @example [{"product_id": 1}, {"product_id": 7, "quantity": 250}]
             */
            'products' => 'required|array',
            /**
             * ส่งตัวแปร shelter_id ของศูนย์พักพิง
             * @var string
             * @example 3
             */
            'shelter_id' => 'required|string|exists:shelters,id',
            /**
             * ส่งตัวแปร ID ของ ทรัพยากรที่ร้องขอ เอาได้จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/product.index
             * @var string
             * @example 3
             */
            'products.*.product_id' => 'required|string|exists:products,id',
            /**
             * จำนวนทรัพยากรที่ร้องขอ หากไม่ระบุจำนวนให้ส่งมา null
             * @var string
             * @example 250
             */
            'products.*.quantity' => 'nullable|integer',
            /**
             * ไม่ต้องส่งอะไรมาหลังบ้านจะ set ให้เอง
             * @var string
             * @example 1
             */
            'products.*.no_quantity' => 'nullable|integer'
        ];
    }

    protected function prepareForValidation()
    {
        $products = $this->input('products', []);
        foreach ($products as $key => $product) {
            if (!isset($product['quantity']) || is_null($product['quantity'])) {
                $products[$key]['no_quantity'] = 1;
            }
        }
        $this->merge([
            'products' => $products,
        ]);
    }
}
