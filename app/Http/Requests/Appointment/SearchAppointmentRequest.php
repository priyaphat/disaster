<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class SearchAppointmentRequest extends FormRequest
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
             * ค้นหา
             * @var string
             */
            'search' => 'nullable|string',
            /**
             * ประเภทของการแสดงผล [1 : ปฏิทิน, 2: ตาราง]
             * @var integer
             * @example 1
             */
            'type' => 'nullable|in:1,2',
            /**
             * เดือนที่ต้องการค้นหา
             * @var string
             * @example '2025-06'
             */
            'selected_month' => 'nullable|date_format:Y-m',
            'selected_date' => 'nullable|date_format:d',
            /**
             * ID ของศูนย์พักพิงที่ต้องการดู
             * @var integer
             * @example 1
             */
            'shelter_id' => 'nullable|exists:shelters,id',
            /**
             * ID ของทีมที่ต้องการดู
             * @var integer
             * @example 1
             */
            'team_id' => 'nullable|exists:teams,id',
        ];
    }
}
