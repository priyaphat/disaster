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
            'prefix_id' => 'required|integer|exists:prefixes,id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'birth_date' => 'required|date',
            'gender' => 'required|in:0,1,2',
            'phone' => 'required|string|size:10',
            'p_address' => 'required|string|max:100',
            'p_geography_id' => 'required|exists:geography,id',
            'c_address' => 'required|string|max:100',
            'c_geography_id' => 'required|exists:geography,id',
            'same_address' => 'required',
            'status' => 'required|in:0,1,2',
            'shelter_id' => 'nullable|exists:shelters,id',
            // 'tracking' => 'required|in:0,1',
            'line_token' => 'nullable|string',
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
