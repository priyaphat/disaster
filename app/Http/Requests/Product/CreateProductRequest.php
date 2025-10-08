<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
                    $unitId = $this->unit_id ?? null;
                    return $query->when($unitId === null, function ($q) {  //เช็คหน่วยที่ว่างด้วย
                        return $q->whereNull('unit_id');
                    }, function ($q) use ($unitId) {
                        return $q->where('unit_id', $unitId);
                    });
                }),
            ],
            'unit_id' => 'nullable|exists:units,id|required_if:unit_type,2',
            'no_unit' => 'nullable',
            // 'category_id' => 'exists:categories,id',
            'source' => 'nullable|string|max:255',
            'detail' => 'nullable|string|max:255',
            'unit_type' => 'nullable|in:1,2',
            'status' => 'integer',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif',
            'group_id' => 'required|in:1,2,3,4,5',
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
