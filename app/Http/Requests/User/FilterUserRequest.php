<?php

namespace App\Http\Requests\User;

use App\Enums\UserType;
use App\Http\Utility\ConvertUtility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterUserRequest extends FormRequest
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
            'district_id' => 'nullable|exists:geography,district_code',
            'department_id' => 'nullable|exists:departments,id',
            'position_id' => 'nullable|exists:positions,id',
            'approve' => 'nullable'
        ];
    }

}
