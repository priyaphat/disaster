<?php

namespace App\Rules;

use App\Enums\ApprovalStatus;
use App\Enums\RegistrationType;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckUserExistsInDB implements ValidationRule
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

        // if (!$user->status) {
        //     $fail(__('validation.exists'));
        // }
        if ($user->registration_type == RegistrationType::SELF->value) {
            if ($user->approval->status != ApprovalStatus::APPROVED->value) {
                $fail(__('validation.exists'));
                return;
            }
        }
    }
}
