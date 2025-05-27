<?php

namespace App\Http\Requests\Team;

use App\Rules\CheckDuplicateMember;
use App\Rules\CheckUserExistsInTeam;
use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
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
            'name' => ['required','max:255'],
            'leader_id' => ['required','exists:users,id', new CheckUserExistsInTeam()],
            'members' => ['required','array'],
            'members.*' => ['required','exists:users,id','distinct', new CheckUserExistsInTeam(), new CheckDuplicateMember($this->input('leader_id'))]
        ];
    }
}
