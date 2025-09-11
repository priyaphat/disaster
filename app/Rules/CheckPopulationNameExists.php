<?php

namespace App\Rules;

use App\Models\Population;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CheckPopulationNameExists implements ValidationRule
{
    protected $prefixId, $firstname, $lastname, $populationId;

    // Accept $id in the constructor (nullable)
    public function __construct($prefixId, $firstname, $lastname, $populationId = null)
    {
        $this->prefixId = $prefixId;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->populationId = $populationId;
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
        if ($this->prefixId && $this->firstname && $this->lastname && !$this->populationId) {
            $existsInUser = Population::where(['first_name' => $this->firstname, 'last_name' => $this->lastname])->exists();
        }

        if ($this->prefixId && $this->firstname && $this->lastname && $this->populationId) {
            $existsInUser = Population::where(['first_name' => $this->firstname, 'last_name' => $this->lastname])->where('id', '!=', $this->populationId)->exists();
        }

        if ($existsInUser) {
            $fail(__('validation.custom.fullname.exists'));
        }
    }
}
