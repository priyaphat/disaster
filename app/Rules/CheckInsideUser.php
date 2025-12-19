<?php

namespace App\Rules;

use App\Enums\UserType;
use App\Models\Geography;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckInsideUser implements ValidationRule
{
    protected $userType;

    // Accept $id in the constructor (nullable)
    public function __construct($userType = null)
    {
        $this->userType = $userType;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        $geo = Geography::where('district_code', $value)->first();
        if ($this->userType && $this->userType == UserType::INSIDE_AREA->value) {
            if ($geo->province_code != 57) {
                $fail(__('validation.in'));
            }
        } else {
            if ($geo->province_code == 57) {
                $fail(__('validation.in'));
            }
        }
    }
}
