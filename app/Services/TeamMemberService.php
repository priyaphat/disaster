<?php

namespace App\Services;

use App\Enums\TeamPosition;
use App\Http\Utility\ConvertUtility;
use App\Models\Position;
use App\Models\TeamMember;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamMemberService
{
    /**
     * Create a Team.
     */
    public function create(array $data): TeamMember
    {
        $member = $this->getMember($data['user_id']);
        if ($member) {
            throw new BadRequestHttpException(__('error.member.have_team'));
        }

        $latestNumber = $this->getLatestNumber($data['team_id']);
        return TeamMember::create([
            'team_id' => $data['team_id'],
            'user_id' => $data['user_id'],
            'team_position' => TeamPosition::MEMBER->value,
            'sequence' => $latestNumber + 1
        ]);
    }

    /**
     * Update a member.
     */
    public function update(array $data): TeamMember
    {
        $member = $this->getMember($data['user_id']);

        if (!$member) {
            throw new BadRequestHttpException(__('error.member.no_team'));
        }

        if ($member->team_position == TeamPosition::LEADER->value) {
            throw new BadRequestHttpException(__('error.member.leader'));
        }

        $count = $this->countOnlyMember($data['user_id'], $data['team_id']);
        if ($count <= 0) {
            throw new BadRequestHttpException(__('error.member.team_min_members'));
        }

        $oldTeam = $member->team_id;
        $newTeam = $data['team_id'];

        $latestNumber = $this->getLatestNumber($newTeam);
        $member->update([
            'team_id' => $data['team_id'],
            'user_id' => $data['user_id'],
            'team_position' => $data['team_position'],
            'sequence' => $latestNumber + 1
        ]);

        $this->reIndex($oldTeam, $member['sequence']);
        return $member;
    }


    /**
     * Delete a member.
     */
    public function delete(array $data): TeamMember
    {
        $member = $this->getMember($data['user_id']);
        if (!$member) {
            throw new BadRequestHttpException(__('error.member.no_team'));
        }

        $teamId = $member->team_id;
        $deleteNumber = $member->sequence;

        if ($member->team_position == TeamPosition::LEADER->value) {
            throw new BadRequestHttpException(__('error.member.leader'));
        }

        $count = $this->countOnlyMember($data['user_id'], $teamId);
        if ($count <= 0) {
            throw new BadRequestHttpException(__('error.member.team_min_members'));
        }


        $member->delete();
        $this->reIndex($teamId, $deleteNumber);
        return $member;
    }

    /**
     * Get a team member by its id.
     */
    public function countOnlyMember(int $userId, ?int $teamId = null)
    {
        $userId = ConvertUtility::toInt($userId);
        $count = TeamMember::where('user_id', '!=', $userId)->where('team_id', $teamId)->where('team_position', TeamPosition::MEMBER->value)->count();
        return $count;
    }

    /**
     * Get a team member by its id.
     */
    public function getMember(int $userId)
    {
        $userId = ConvertUtility::toInt($userId);
        $query = TeamMember::where('user_id', $userId);
        return $query->first();
    }

    public function getLatestNumber($teamId)
    {
        $teamId = ConvertUtility::toInt($teamId);
        return TeamMember::where('team_id', $teamId)->max('sequence') ?? 0;
    }

    public function reIndex($teamId, $deleteNumber)
    {
        $teamId = ConvertUtility::toInt($teamId);
        $deleteNumber = ConvertUtility::toInt($deleteNumber);
        TeamMember::where('team_id', $teamId)->where('sequence', '>', $deleteNumber)
            ->decrement('sequence');
    }
}
