<?php

namespace App\Http\Requests\ShelterPosition;

use Illuminate\Foundation\Http\FormRequest;

class CreateShelterPositionRequest extends FormRequest
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
             * ชื่อตำแหน่งที่่ต้องการสร้าง
             * @var string
             * @example พลอากาศเอก
             */
            'name' => 'required|unique:shelter_staff_positions,name|max:255',
        ];
    }
}
