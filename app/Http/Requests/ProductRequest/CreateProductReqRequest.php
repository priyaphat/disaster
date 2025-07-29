<?php

namespace App\Http\Requests\ProductRequest;

use App\Http\Utility\ProductUtility;
use App\Models\ProductRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProductReqRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // return [
        //     'data' => 'required|array',
        //     'data.*.product_id' => [
        //         'required',
        //         'exists:products,id',
        //         Rule::unique('product_requests') // ห้าม product_id ซ้ำพร้อม กับ shelter_id
        //             ->where(function ($query) {
        //                 return $query->where('shelter_id', ProductUtility::getShelter())
        //                     ->where('status', ProductRequest::ACTIVE);
        //             })
        //     ],
        //     'data.*.request' => 'numeric|min:-1',
        // ];
        return [
            '*.product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('product_requests')
                    ->where(function ($query) {
                        return $query->where('shelter_id', ProductUtility::getShelter())
                            ->where('status', ProductRequest::ACTIVE);
                    })
            ],
            '*.request' => 'nullable|numeric|min:-1',
        ];
    }

    public function messages()
    {
        return [
            '*.product_id.unique' => __('error.product_required.add_unique')
        ];
    }
}