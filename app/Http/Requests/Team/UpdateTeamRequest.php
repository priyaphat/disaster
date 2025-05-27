<?php

namespace App\Http\Requests\Team;

use App\Rules\CheckDuplicateMember;
use App\Rules\CheckUserExistsInTeam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
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
        $id = $this->route('team')->id;

        return [
            'name' => ['required', 'max:255'],
            'leader_id' => ['required', 'exists:users,id', new CheckUserExistsInTeam($id)],
            'members' => ['required', 'array'],
            'members.*' => ['required', 'exists:users,id', 'distinct', new CheckUserExistsInTeam($id), new CheckDuplicateMember($this->input('leader_id'))]
        ];
    }
}
