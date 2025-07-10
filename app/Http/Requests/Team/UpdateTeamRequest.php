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
            /**
             * ชื่อทีม
             * @var string
             * @example "ทีมงานประสบภัย แก้ไข"
             */
            'name' => ['required', 'max:255', Rule::unique('shelters', 'name')->ignore($id)],
            /**
             * หัวหน้าทีม : user_id | เอามาจาก users
             * @var integer
             * @example 1
             */
            'leader_id' => ['required', 'exists:users,id', new CheckUserExistsInTeam($id)],
            /**
             * สมาชิกในทีม : user_id | เอามาจาก users
             * @var array
             * @example [1,2,3]
             */
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
            error_log($index);
            return [
                'user_id' => $item,
                'team_position' => $index == 0 ? TeamPosition::LEADER : TeamPosition::MEMBER,
                'sequence' => $index,
            ];
        })->toArray();

        return [
            'name' => $this->input('name'),
            'members' => $memberArray,
        ];
    }
}
