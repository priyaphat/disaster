<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required_if:status,1',
                'max:255',
                Rule::unique('products')->where(function ($query) {
                    return $query
                        ->where('unit_id', $this->unit_id)
                        ->where('category_id', $this->category_id);
                }),
            ],
            'unit_id' => 'nullable|exists:units,id',
            'no_unit' => 'nullable',
            'category_id' => 'exists:categories,id',
            'detail' => 'nullable|string',
            'unit_type' => 'nullable|in:1,2',
            'status' => 'integer|in:0,1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:1024'
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => __('validation.custom.product.name'),
        ];
    }
}
