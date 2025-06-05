<?php

namespace App\Http\Requests\Team;

use App\Enums\TeamPosition;
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

    public function normalized(): array
    {
        $leader = $this->input('leader_id');
        $members = $this->input('members');
        $mergedMember = collect([$leader])->merge($members);

        // Define index = 0 is leader
        $memberArray = $mergedMember->map(function ($item, $index) {
            return [
                'user_id' => $item,
                'position' => $index == 0 ? TeamPosition::LEADER : TeamPosition::MEMBER,
                'sequence' => $index,
            ];
        })->toArray();

        return [
            'name' => $this->input('name'),
            'members' => $memberArray,
        ];
    }
}
