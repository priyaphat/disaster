<?php

namespace App\Http\Requests\User;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|unique:users,email|email|max:255',
            'username' => 'required|unique:users,username|max:255',
            'prefix_id' => 'required|exists:prefixes,id',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'phone' => 'required|max:10',
            'type' => ['required', Rule::enum(UserType::class)],
            'location_id' => 'required',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id'
        ];
    }
}
