<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
             * วันที่ที่ต้องการนัดหมาย
             * @var string
             * @example 2025-06-06
             */
            'appointment_date' => 'required|date|date_format:Y-m-d',
            /**
             * เวลาเริ่มต้น
             * @var string
             * @example 07:30
             */
            'start_time' => 'required|date_format:H:i',
            /**
             * เวลาสิ้นสุด
             * @var string
             * @example 11:59
             */
            'end_time'   => 'required|date_format:H:i|after:start_time',
            /**
             * ID ของศูนย์พักพิงที่ต้องการนัดหมาย
             * @var integer
             * @example 1
             */
            'shelter_id' => 'required|exists:shelters,id',
            /**
             * ID ของทีมที่ต้องการนัดหมาย
             * @var integer
             * @example 1
             */
            'team_id' => 'required|exists:teams,id'
        ];
    }

    public function messages()
    {
        return [
            'end_time.after' => __('validation.custom.endtime.after')
        ];
    }
}
