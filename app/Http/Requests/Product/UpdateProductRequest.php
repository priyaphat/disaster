<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'name' => [
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
            'quantity' => 'nullable|integer',
            'no_quantity' => 'nullable|integer',
            'status' => 'integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:1024'
        ];
    }
}
