<?php

namespace App\Http\Requests\Appointment;

use App\Http\Utility\DateUtillity;
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
            'search' => 'nullable|string',
            'type' => 'nullable',
            'selected_month' => 'nullable',
            'shelter_id' => 'nullable|exists:shelters,id',
            'team_id' => 'nullable|exists:teams,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->request->get('selected_month')) {
            $this->merge([
                'selected_month' => DateUtillity::thaiToAD($this->request->get('selected_month'))
            ]);
        }
    }
}
