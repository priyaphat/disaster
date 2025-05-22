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
        $is_approved = $this->input('is_approved');
        return [
            'is_approved' => ['required', Rule::enum(ApprovalStatus::class)],
            'approved_reason' => [Rule::requiredIf($is_approved ==  ApprovalStatus::REJECTED->value)],
        ];
    }
}
