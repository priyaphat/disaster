<?php

namespace App\Rules;

use App\Models\Team;
use App\Models\TeamMember;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckUserExistsInTeam implements ValidationRule
{
    protected $id;

    // Accept $id in the constructor (nullable)
    public function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        if ($this->id) {
            // This is an update (id present)
            $existsInTeam = TeamMember::where('user_id', $value)->where('team_id', '!=', $this->id)->exists();
        } else {
            // This is a create
            $existsInTeam = TeamMember::where('user_id', $value)->exists();
        }

        if ($existsInTeam) {
            $fail("{$attribute} มีทีมอยู่แล้ว");
        }
    }
}
