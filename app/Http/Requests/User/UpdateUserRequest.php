<?php

namespace App\Http\Requests\User;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class UpdateUserRequest extends FormRequest
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
            'email' => [
                'required',
                'max:255',
                'email',
                'regex:/^[a-zA-Z0-9._-]*[a-zA-Z0-9]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('users')->where(fn(Builder $query) => $query->where('email', Str::lower($this->email)))->ignore($this->user->id)
            ],
            'username' => [
                'required',
                'max:255',
                'regex:/^[a-zA-Z][a-zA-Z0-9._-]{3,28}[a-zA-Z0-9]$/',
                Rule::unique('users')->where(fn(Builder $query) => $query->where('username', Str::lower($this->username)))->ignore($this->user->id)
            ],
            'prefix_id' => 'required|exists:prefixes,id',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'phone' => 'required|max:10',
            'type' => ['required', Rule::enum(UserType::class)],
            'district_id' => 'required|exists:geography,district_code',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id'
        ];
    }

    public function messages()
    {
        return [
            'email.regex' => __('validation.custom.email.regex'),
            'username.regex' => __('validation.custom.username.regex'),
        ];
    }
}
