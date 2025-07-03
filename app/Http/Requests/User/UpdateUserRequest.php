<?php

namespace App\Http\Requests\User;

use App\Enums\UserType;
use App\Rules\CheckFullNameExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
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
        $user = $this->user ?? Auth::user();
        return [
            'email' => [
                'required',
                'max:255',
                'regex:/^[a-zA-Z][a-zA-Z0-9._-]*[a-zA-Z0-9]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('users')->where(fn(Builder $query) => $query->where('email', Str::lower($this->email)))->ignore($user->id)
            ],
            'username' => [
                'required',
                'regex:/^[a-zA-Z][a-zA-Z0-9._-]{3,29}$/',
                Rule::unique('users')->where(fn(Builder $query) => $query->where('username', Str::lower($this->username)))->ignore($user->id)
            ],
            'prefix_id' => 'required|exists:prefixes,id',
            'firstname' => ['required', 'max:255', new CheckFullNameExists($this->firstname, $this->lastname, $user->id)],
            'lastname' => ['required', 'max:255', new CheckFullNameExists($this->firstname, $this->lastname, $user->id)],
            'phone' => 'required|regex:/^\d{10}$/',
            'type' => ['required', Rule::enum(UserType::class)],
            'district_id' => 'required|exists:geography,district_code',
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
