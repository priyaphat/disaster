<?php

namespace App\Http\Requests\User;

use App\Enums\UserType;
use App\Rules\CheckFullNameExists;
use App\Rules\CheckInsideUser;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class RegisterRequest extends FormRequest
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
                'regex:/^[a-zA-Z][a-zA-Z0-9._-]*[a-zA-Z0-9]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('users')->where(fn(Builder $query) => $query->where('email', Str::lower($this->email)))
            ],
            'username' => [
                'required',
                'regex:/^[a-zA-Z][a-zA-Z0-9._-]{3,29}$/',
                Rule::unique('users')->where(fn(Builder $query) => $query->where('username', Str::lower($this->username)))
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'password_confirmation' => 'required',
            'prefix_id' => 'required|exists:prefixes,id',
            'firstname' => ['required', 'max:255', new CheckFullNameExists($this->firstname, $this->lastname)],
            'lastname' => ['required', 'max:255', new CheckFullNameExists($this->firstname, $this->lastname)],
            'phone' => 'required|regex:/^\d{10}$/',
            'type' => ['required', Rule::enum(UserType::class)],
            'district_id' => ['required','exists:geography,district_code', new CheckInsideUser($this->type)],
            'department_id' => [Rule::requiredIf($this->type == UserType::INSIDE_AREA->value), 'nullable', 'exists:departments,id'],
            'position_id' => 'required|exists:positions,id'
        ];
    }

    public function messages()
    {
        return [
            'email.regex' => __('validation.custom.email.regex'),
            'username.regex' => __('validation.custom.username.regex'),
            'phone.regex' => __('validation.custom.phone.regex'),
        ];
    }
}