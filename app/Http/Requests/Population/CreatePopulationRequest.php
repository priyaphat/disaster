<?php

namespace App\Http\Requests\Population;

use App\Enums\AllergyStatus;
use App\Enums\DeathCause;
use App\Enums\PopulationStatus;
use App\Rules\CheckPopulationNameExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'first_name' => ['required', 'string', 'max:100', new CheckPopulationNameExists($this->prefix_id, $this->first_name, $this->last_name, null)],
            /**
             * นามสกุล
             * @var string
             * @example รวย
             */
            'last_name' => ['required', 'string', 'max:100', new CheckPopulationNameExists($this->prefix_id, $this->first_name, $this->last_name, null)],
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
            'status' => ['required', Rule::enum(PopulationStatus::class)],
            /**
             * ส่งรายละเอียดการเสียชีวิต
             * @var integer
             * @example 1
             */
            'death_cause' => ['nullable', 'required_if:status,' . PopulationStatus::DEATH->value, Rule::enum(DeathCause::class)],
            /**
             * ส่งตัวแปร shelter_id ของศูนย์พักพิง
             * @var string
             * @example 3
             */
            'shelter_id' => 'nullable|exists:shelters,id',
            // /**
            //  * ส่ง id ของกลุ่มเปราะบาง
            //  * @var string
            //  * @example 3
            //  */
            // 'vulnerable_group_id' => 'nullable',
            /**
             * ส่ง array รายละเอียดการแพ้
             * @var array
             * @example []
             */
            'allergy' => 'nullable|array',
            'allergy.status' => ['required_if:allergy,array', Rule::enum(AllergyStatus::class)],
            'allergy.drug' => 'nullable',
            'allergy.food' => 'nullable',
            'allergy.other' => 'nullable',
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

    // /**
    //  * Prepare the data for validation.
    //  */
    // protected function prepareForValidation(): void
    // {
    //     if ($this->has('vulnerable_group_id') && $this->input('vulnerable_group_id') <= 0) {
    //         $this->merge(['vulnerable_group_id' => null]);
    //     }
    // }

    protected function passedValidation()
    {
        if ($this->has('status') && $this->input('status') != PopulationStatus::DEATH->value) {
            $this->merge(['death_cause' => null]);
        }

        if ($this->has('allergy.status') && $this->input('allergy.status') != AllergyStatus::ALLERGIC->value) {
            $this->merge([
                'allergy.drug' => null,
                'allergy.food' => null,
                'allergy.other' => null,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'phone.size' => 'โปรดกรอกเบอร์โทรศัพท์ที่ติดต่อ ให้ถูกต้อง',
        ];
    }
}
