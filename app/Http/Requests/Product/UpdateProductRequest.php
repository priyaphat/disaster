<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
                    $unitId = $this->unit_id ?? null;
                    return $query->when($unitId === null, function ($q) { //เช็คหน่วยที่ว่างด้วย
                        return $q->whereNull('unit_id');
                    }, function ($q) use ($unitId) {
                        return $q->where('unit_id', $unitId);
                    });
                })->ignore($this->product->id),

            ],
            'unit_id' => 'nullable|exists:units,id',
            'no_unit' => 'nullable',
            // 'category_id' => 'exists:categories,id',
            'detail' => 'nullable|string|max:255',
            'unit_type' => 'nullable|in:1,2',
            'unit_id' => 'required_if:unit_type,2|nullable|exists:units,id|',
            'status' => 'integer|in:0,1',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif',
            'group_id' => 'required|in:1,2,3,4',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => __('validation.custom.product.name'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $failed = $validator->failed();
        $badRequest = isset($failed['name']['Unique']);

        if ($badRequest) {
            $message = collect($validator->errors())->flatten()->first();
            throw new BadRequestHttpException($message);
        }

        throw new ValidationException($validator);
    }
}
