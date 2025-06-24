<?php

namespace App\Http\Requests\Population;

use Illuminate\Foundation\Http\FormRequest;

class CreatePopulationRequest extends FormRequest
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
             * ID ของ prefixs ได้จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/prefix.index
             * @var string
             * @example 1
             */
            'prefix_id' => 'required|integer|exists:prefixes,id',
            /**
             * ชื่อ
             * @var string
             * @example มา
             */
            'first_name' => 'required|string|max:100',
            /**
             * นามสกุล
             * @var string
             * @example รวย
             */
            'last_name' => 'required|string|max:100',
            /**
             * วันเกิด
             * @var datetime
             * @example 2025-06-24 13:10:00
             */
            'birth_date' => 'required|date',
            /**
             * เพศ 0 = ไม่ระบุเพศ, 1 =  ชาย, 2 = หญิง
             * @var string
             * @example 1
             */
            'gender' => 'required|in:0,1,2',
            /**
             * เบอร์มือถือ
             * @var string
             * @example 081234567
             */
            'phone' => 'required|string|size:10',
            /**
             * รายละเอียดของภูมิลำเนา
             * @var string
             * @example 87/2 บ้านสันทราย
             */
            'p_address' => 'required|string|max:100',
            /**
             * ID ของภูมิลำเนา ใช้ geography_id จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/address.getAddress
             * @var string
             * @example 4826
             */
            'p_geography_id' => 'required|exists:geography,id',
            /**
             * รายละเอียดของที่อยู่ปัจจุบัน
             * @var string
             * @example 87/2 บ้านสันทราย
             */
            'c_address' => 'required|string|max:100',
            /**
             * ID ของที่อยู่ปัจจุบัน ใช้ geography_id จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/address.getAddress
             * @var string
             * @example 4826
             */
            'c_geography_id' => 'required|exists:geography,id',
            /**
             * หากที่อยู่ปัจจุบัน และ ภูมิลำเนา เหมือนกัยให้ส่ง 1 มา ไม่เหมือน 0
             * @var string
             * @example 1
             */
            'same_address' => 'required',
            /**
             * สถานะ 0 - เสียชีวิต , 1 - บาดเจ็บ , 2 - ปกติ
             * @var string
             * @example 1
             */
            'status' => 'required|in:0,1,2',
            /**
             * ส่งตัวแปร shelter_id ของศูนย์พักพิง
             * @var string
             * @example 3
             */
            'shelter_id' => 'nullable|exists:shelters,id',
            // 'tracking' => 'required|in:0,1',
            /**
             * Line token
             * @var string
             * @example 3e12as@dAS3tgsdfg423f
             */
            'line_token' => 'nullable|string',
            /**
             * วันที่บันทึกข้อมูลลงระบบ
             * @var datetime
             * @example 2025-06-24 13:10:00
             */
            'created_date' => 'required|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('same_address') == 1) {
                if (
                    $this->input('p_address') !== $this->input('c_address') ||
                    $this->input('p_geography_id') != $this->input('c_geography_id')
                ) {
                    $validator->errors()->add('same_address', 'ที่อยู่ตามทะเบียนบ้านและที่อยู่ปัจจุบันต้องเหมือนกัน');
                }
            }
        });
    }
}
