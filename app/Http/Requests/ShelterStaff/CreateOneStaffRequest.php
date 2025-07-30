<?php

namespace App\Http\Requests\ShelterStaff;

use App\Rules\CheckVerifiedUserExists;
use Illuminate\Foundation\Http\FormRequest;

class CreateOneStaffRequest extends FormRequest
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'shelter_id' => 'required|exists:shelters,id',
            'position_id' => 'required|exists:shelter_positions,id',
        ];
    }

    public function normalized(): array
    {
        return [
            'user_id' => $this->input('user_id'),
            'shelter_id' => $this->input('shelter_id'),
            'shelter_position_id' => $this->input('position_id')
        ];
    }
}
