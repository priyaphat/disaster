<?php

namespace App\Http\Requests\ProductRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductReqRequest extends FormRequest
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
             * จำนวนทรัพยากรที่ร้องขอ หากไม่ระบุจำนวนให้ส่งมา null
             * @var string
             * @example 250
             */
            'quantity' => 'nullable|integer',
            /**
             * ไม่ต้องส่งอะไรมาหลังบ้านจะ set ให้เอง
             * @var string
             * @example 0
             */
            'no_quantity' => 'nullable|integer'
        ];
    }

    protected function prepareForValidation()
    {
        if (!isset($this->quantity) || is_null($this->quantity)) {
            $this->merge([
                'no_quantity' => 1,
            ]);
        }
    }
}
