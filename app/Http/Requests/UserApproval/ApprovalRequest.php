<?php

namespace App\Http\Requests\UserApproval;

use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApprovalRequest extends FormRequest
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
             * ID ของผู้ใช้งาน
             * @var integer
             * @example 1
             */
            'user_id' => ['required', 'exists:users,id'],
            /**
             * สถานะอนุมัติคำขอลงทะเบียน (1 อนุมัติ, 2 ปฏิเสธ)
             * @var integer
             * @example 1
             */
            'is_approved' => ['required', Rule::enum(ApprovalStatus::class)],
        ];
    }
}
