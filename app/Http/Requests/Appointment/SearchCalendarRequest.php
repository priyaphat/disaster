<?php

namespace App\Http\Requests\Appointment;

use App\Http\Utility\DateUtillity;
use Illuminate\Foundation\Http\FormRequest;

class SearchCalendarRequest extends FormRequest
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
             * วันที่ที่ต้องการค้นหา
             * @var string
             * @example 2025-06-01
             */
            'selected_date' => 'nullable|date_format:Y-m-d',
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
