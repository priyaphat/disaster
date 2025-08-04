<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductRequest extends FormRequest
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
                'required',
                'max:255',
                Rule::unique('products')->where(function ($query) {
                    return $query
                        ->where('unit_id', $this->unit_id)
                        ->where('category_id', $this->category_id);
                }),
            ],
            'unit_id' => 'nullable|exists:units,id',
            'no_unit' => 'nullable',
            // 'category_id' => 'exists:categories,id',
            // 'detail' => 'nullable|string',
            'unit_type' => 'required|in:1,2',
            'status' => 'integer',
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
