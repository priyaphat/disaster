<?php

namespace App\Rules;

use App\Enums\ApprovalStatus;
use App\Enums\RegistrationType;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckVerifiedUserExists implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        $user = User::with('approval')->find($value);

        if (!$user) {
            $fail(__('validation.exists'));
            return;
        }

        if ($user->registration_type?->value == RegistrationType::SELF->value) {
            if ($user->approval?->is_approved != ApprovalStatus::APPROVED->value) {
                $fail(__('validation.exists'));
                return;
            }
        }
    }
}
