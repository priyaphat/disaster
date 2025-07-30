<?php

namespace App\Http\Requests\Appointment;

use App\Rules\CheckVerifiedUserExists;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentLogRequest extends FormRequest
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
             * array ของ user_id
             */
            'old_users' => 'required|array|min:2',
            /**
             * id ของผู้ใช้งาน
             * @var int
             * @example 1
             */
            'old_users.*' => 'required|distinct',
            /**
             * array ของ new_users
             */
            'new_users' => 'nullable|array',
            /**
             * id ของสมาชิกที่ถูกเพิ่มเข้ามา
             * @var int
             * @example 1
             */
            'new_users.*' => ['nullable', 'distinct', new CheckVerifiedUserExists()],
        ];
    }
}
