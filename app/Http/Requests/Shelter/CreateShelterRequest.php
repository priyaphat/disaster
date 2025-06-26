<?php

namespace App\Http\Requests\Shelter;

use Illuminate\Foundation\Http\FormRequest;

class CreateShelterRequest extends FormRequest
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
             * ชื่อศูนย์พักพิงที่่ต้องการสร้าง
             * @var string
             * @example ศูนย์พักใจ
             */
            'name' => 'required|string|max:255|unique:shelters,name',
            /**
             * เบอร์โทร
             * @var string
             * @example 081234567
             */
            'phone' => 'required|string|max:10',
            /**
             * วันที่สร้างศูนย์พักพิง
             * @var datetime
             * @example 2025-06-24 13:10:00
             */
            'created_date' => 'required|date',
            /**
             * หัวหน้าศูนย์พักพิง ใช้ user_id
             * @var string
             * @example 10
             */
            'leader_id' => 'required|string|exists:users,id',
            /**
             * ID ของที่อยู่ ใช้ geography_id จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/address.getAddress
             * @var string
             * @example 4826
             */
            'geography_id' => 'required|string|exists:geography,id',
            /**
             * Code ของจังหวัด ใช้ province_code จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/address.getProvinces
             * @var string
             * @example 57
             */
            'province_code' => 'required|string|exists:geography,province_code',
            /**
             * Code ของอำเภอ ใช้ district_code จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/address.getDistricts
             * @var string
             * @example 5701
             */
            'district_code' => 'required|string|exists:geography,district_code',
            /**
             * Code ของตำบล ใช้ subdistrict_code จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/address.getSubDistricts
             * @var string
             * @example 5701
             */
            'subdistrict_code' => 'required|string|exists:geography,subdistrict_code',
            /**
             * ID ของ สาธารณูปโภคภายในศูนย์พักพิง จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/shelter.utility
             * @var array
             * @example ["1", "2"]
             */
            'utilities' => 'required|array',
            /**
             * รายละเอียดของที่อยู่
             * @var string
             * @example 87/2 บ้านสันทราย
             */
            'address' => 'required|string',
            /**
             * จำนวนความจุของผู้ประสบภัยในศูนย์พักพิง
             * @var string
             * @example 200
             */
            'max_capacity' => 'required|string',
        ];
    }
}
