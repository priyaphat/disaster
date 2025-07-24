<?php

namespace App\Http\Requests\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
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
            'name' => 'required|string|max:255|' . Rule::unique('units', 'name')->ignore($this->unit->id),
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => __('validation.custom.unit.name'),
        ];
    }
}
