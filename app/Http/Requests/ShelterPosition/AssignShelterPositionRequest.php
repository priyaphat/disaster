<?php

namespace App\Http\Requests\ShelterPosition;

use Illuminate\Foundation\Http\FormRequest;

class AssignShelterPositionRequest extends FormRequest
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
             * ส่งตัวแปร shelter_position_id ของตำแหน่งในศูนย์พักพิง สามารถดูได้จาก https://disaster-backend.gentechsoft.tech/api/docs#/operations/shelterPosition.index
             * @var string
             * @example 3
             */
            'shelter_position_id' => 'required|exists:shelter_positions,id',
        ];
    }
}
