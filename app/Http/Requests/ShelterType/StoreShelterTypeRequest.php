<?php

namespace App\Http\Requests\ShelterType;

use Illuminate\Foundation\Http\FormRequest;

class StoreShelterTypeRequest extends FormRequest
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
            'name' => 'required|unique:shelter_types,name|max:255'
        ];
    }
}
