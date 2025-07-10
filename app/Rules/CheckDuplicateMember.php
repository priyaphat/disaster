<?php

namespace App\Rules;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckDuplicateMember implements ValidationRule
{
    protected $leaderId;

    // Accept $id in the constructor (nullable)
    public function __construct($id = null)
    {
        $this->leaderId = $id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        if ($this->leaderId && $this->leaderId === $value) {
            $user = User::select('id', 'prefix_id', 'firstname', 'lastname')->find($value);
            $fail($user->fullname.' '. 'เป็นหัวหน้าทีมแล้ว');
        }
    }
}
