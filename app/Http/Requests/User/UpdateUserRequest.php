<?php

namespace App\Http\Requests\User;

use App\Enums\UserType;
use App\Rules\CheckFullNameExists;
use App\Rules\CheckInsideUser;
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
        $user = $this->user;
        return [
            'prefix_id' => 'required|exists:prefixes,id',
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'phone' => [
                'required',
                'regex:/^\d{10}$/',
                Rule::unique('users')->where(fn(Builder $query) =>
                $query->where('phone', $this->phone)
                    ->whereNull('deleted_at'))
                    ->ignore($user->id)
            ],
            'type' => ['required', Rule::enum(UserType::class)],
            'district_id' => ['required', 'exists:geography,district_code', new CheckInsideUser($this->type)],
            'department_id' => [Rule::requiredIf($this->type == UserType::INSIDE_AREA->value), 'nullable', 'exists:departments,id'],
            'position_id' => 'required|exists:positions,id'
        ];
    }

    public function messages()
    {
        return [
            'phone.regex' => __('validation.custom.phone.regex'),
            'phone.unique' => __('validation.custom.phone.unique')
        ];
    }

    public function normalized(): array
    {
        return [
            'prefix_id' => $this->input('prefix_id'),
            'firstname' => $this->input('firstname'),
            'lastname' => $this->input('lastname'),
            'phone' => $this->input('phone'),
            'type' => $this->input('type'),
            'district_id' => $this->input('district_id'),
            'department_id' => $this->input('department_id'),
            'position_id' => $this->input('position_id'),
        ];
    }
}
