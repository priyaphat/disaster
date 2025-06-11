<?php

namespace App\Http\Requests\Appointment;

use App\Http\Utility\DateUtillity;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'appointment_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'shelter_id' => 'required|exists:shelters,id',
            'team_id' => 'required|exists:teams,id'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'appointment_date' => DateUtillity::thaiToAD($this->request->get('appointment_date')) 
        ]);
    }
}
