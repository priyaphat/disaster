<?php

namespace App\Http\Requests\Population;

use Illuminate\Foundation\Http\FormRequest;

class ExitAllPopulationRequest extends FormRequest
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
             * ส่งตัวแปร shelter_id ของศูนย์พักพิง
             * @var string
             * @example 3
             */
            'shelter_id' => 'required|exists:shelters,id',
        ];
    }
}
