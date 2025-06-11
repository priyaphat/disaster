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
            'name' => 'required|max:255|unique:shelters,name',
            'phone' => 'required|max:10',
            'created_date' => 'required',
            'leader_id' => 'required|exists:users,id',
            'geography_id' => 'required|exists:geography,id',
            'province_code' => 'required|exists:geography,province_code',
            'district_code' => 'required|exists:geography,district_code',
            'subdistrict_code' => 'required|exists:geography,subdistrict_code',
            'utilities' => 'required',
            'address' => 'required',
            'max_capacity' => 'required',
        ];
    }
}
