<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckFullNameExists implements ValidationRule
{
    protected $firstname, $lastname, $userId;

    // Accept $id in the constructor (nullable)
    public function __construct($firstname, $lastname, $userId = null)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
         $this->userId = $userId;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        $existsInUser = false;
        if($this->firstname && $this->lastname && !$this->userId){
            $existsInUser = User::where(['firstname' => $this->firstname, 'lastname' => $this->lastname])->whereNull('deleted_at')->exists();
        }

        if($this->firstname && $this->lastname && $this->userId){
            $existsInUser = User::where(['firstname' => $this->firstname, 'lastname' => $this->lastname])->whereNull('deleted_at')->where('id', '!=', $this->userId)->exists();
        }

        if ($existsInUser) {
            $fail(__('validation.custom.fullname.exists'));
        }
    }
}
