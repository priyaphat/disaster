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
            'products' => 'required|array',
            'shelter_id' => 'required|exists:shelters,id',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'nullable|integer',
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
