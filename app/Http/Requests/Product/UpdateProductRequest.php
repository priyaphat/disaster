<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
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
            /**
             * ชื่อทรัพยากรที่ต้องการสร้าง
             * @var string
             * @example 250
             */
            'name' => [
                'max:255',
                Rule::unique('products')->where(function ($query) {
                    return $query
                        ->where('unit_id', $this->unit_id)
                        ->where('category_id', $this->category_id);
                }),
            ],
            /**
             * ID ของ unit_id หน่วยของทรัพยากร หาได้จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/unit.index
             * @var string
             * @example 1
             */
            'unit_id' => 'nullable|exists:units,id',
            /**
             * หากไม่มี unit_id ให้เป็น null
             * @var string
             * @example null
             */
            'no_unit' => 'nullable',
            /**
             * ID ของ category_id ประเภทของทรัพยากร หาได้จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/category.index
             * @var string
             * @example 2
             */
            'category_id' => 'exists:categories,id',
            /**
             * รายละเอียดทรัพยากร
             * @var string
             * @example ได้จากการบริจาค
             */
            'detail' => 'nullable|string',
            /**
             * จำนวนทรัพยากร หากไม่ระบุจำนวนให้ส่งมา null
             * @var string
             * @example 250
             */
            'quantity' => 'nullable|integer',
            /**
             * ส่ง null หากไม่ระบุจำนวน
             * @var string
             * @example 0
             */
            'no_quantity' => 'nullable|integer',
            /**
             * เปิด = 1 ปิด = 0 ใช้งานทรัพยากร 
             * @var string
             */
            'status' => 'integer',
            /**
             * รูป
             * @var image
             */
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:1024'
        ];
    }

    protected function prepareForValidation()
    {
        if (is_null($this->quantity)) {
            $this->merge([
                'no_quantity' => Product::NO_QUANTITY,
            ]);
        }
    }
}
